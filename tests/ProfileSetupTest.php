<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\User;
use App\Industry;
use App\Client;
use Illuminate\Support\Facades\Validator;

class ProfileSetupTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;
    protected $industry;
    protected $client;

    public function setUp()
    {
        parent::setUp();
        
        // Disable CSRF protection for tests
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);
        
        // Ensure we have a language record
        $language = \App\Language::first();
        if (!$language) {
            $language = \App\Language::create([
                'name' => 'English',
                'native_name' => 'English',
                'code' => 'en'
            ]);
        }
        
        // Create test industry with unique name
        $this->industry = Industry::create([
            'name' => 'Test Technology Industry ' . uniqid()
        ]);

        // Create test client with unique name
        $this->client = Client::create([
            'name' => 'Test Client ' . uniqid(),
            'require_profile' => true,
            'require_research' => true
        ]);

        // Create test user with unique email
        $this->user = User::create([
            'username' => 'testuser_' . uniqid(),
            'name' => 'John Doe',
            'email' => 'john_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'client_id' => $this->client->id,
            'language_id' => $language->id, // Use the actual language ID
            'completed_profile' => false,
            'completed_research' => false
        ]);
    }

    /**
     * Test profile page access for new user
     */
    public function testProfilePageAccessForNewUser()
    {
        $this->actingAs($this->user);

        $response = $this->call('GET', '/profile');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('Create Your Profile', $response->getContent());
        $this->assertContains('Test Technology Industry', $response->getContent()); // Industry should be in dropdown
    }

    /**
     * Test profile page redirects when user has completed profile
     */
    public function testProfilePageRedirectsWhenCompleted()
    {
        $this->user->completed_profile = true;
        $this->user->save();

        $this->actingAs($this->user);

        $response = $this->call('GET', '/profile');

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertContains('/profile/research', $response->getTargetUrl());
    }

    /**
     * Test profile page redirects when user has no language
     */
    public function testProfilePageRedirectsWhenNoLanguage()
    {
        $this->user->language_id = null;
        $this->user->save();

        $this->actingAs($this->user);

        $response = $this->call('GET', '/profile');

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertContains('/language', $response->getTargetUrl());
    }

    /**
     * Test profile page redirects when user has completed both profile and research
     */
    public function testProfilePageRedirectsWhenAllCompleted()
    {
        $this->user->completed_profile = true;
        $this->user->completed_research = true;
        $this->user->save();

        $this->actingAs($this->user);

        $response = $this->call('GET', '/profile');

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertContains('/assignments', $response->getTargetUrl());
    }

    /**
     * Test successful profile update
     */
    public function testSuccessfulProfileUpdate()
    {
        $this->actingAs($this->user);

        $data = [
            'first_name' => 'Jane',
            'middle_name' => 'Marie',
            'last_name' => 'Smith',
            'email' => 'jane@example.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
            'industry_id' => $this->industry->id
        ];

        $response = $this->call('POST', '/profile', $data);

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertContains('/profile/research', $response->getTargetUrl());

        // Refresh user from database
        $this->user = User::find($this->user->id);

        // Assert user data was updated
        $this->assertEquals('Jane Marie Smith', $this->user->name);
        $this->assertEquals('jane@example.com', $this->user->email);
        $this->assertEquals($this->industry->id, $this->user->industry_id);
        $this->assertTrue((bool)$this->user->completed_profile);
        $this->assertTrue(Hash::check('newpassword123', $this->user->password));
    }

    /**
     * Test profile update with validation errors
     */
    public function testProfileUpdateWithValidationErrors()
    {
        $this->actingAs($this->user);

        $data = [
            'first_name' => '', // Required field missing
            'last_name' => '', // Required field missing
            'email' => 'invalid-email', // Invalid email
            'password' => 'short', // Too short
            'password_confirmation' => 'different', // Doesn't match
            'industry_id' => 999 // Non-existent industry
        ];

        $response = $this->call('POST', '/profile', $data);

        $this->assertEquals(302, $response->getStatusCode());
        
        // Should redirect back with errors
        $this->assertTrue($response->isRedirection());
        
        // Check that user was not updated
        $this->user = User::find($this->user->id);
        $this->assertFalse((bool)$this->user->completed_profile);
    }

    /**
     * Test profile update with optional middle name
     */
    public function testProfileUpdateWithOptionalMiddleName()
    {
        $this->actingAs($this->user);

        $data = [
            'first_name' => 'Alice',
            'middle_name' => '', // Empty middle name
            'last_name' => 'Johnson',
            'email' => 'alice@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'industry_id' => $this->industry->id
        ];

        $response = $this->call('POST', '/profile', $data);

        $this->assertEquals(302, $response->getStatusCode());

        $this->user = User::find($this->user->id);
        $this->assertEquals('Alice Johnson', $this->user->name); // Should not include empty middle name
    }

    /**
     * Test profile update with optional industry
     */
    public function testProfileUpdateWithOptionalIndustry()
    {
        $this->actingAs($this->user);

        $data = [
            'first_name' => 'Bob',
            'last_name' => 'Wilson',
            'email' => 'bob@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'industry_id' => '' // Empty industry
        ];

        $response = $this->call('POST', '/profile', $data);

        $this->assertEquals(302, $response->getStatusCode());

        $this->user = User::find($this->user->id);
        $this->assertNull($this->user->industry_id);
        $this->assertTrue((bool)$this->user->completed_profile);
    }

    /**
     * Test name parsing logic in profile method
     */
    public function testNameParsingLogic()
    {
        $this->actingAs($this->user);

        // Test single name
        $this->user->name = 'John';
        $this->user->save();

        $response = $this->call('GET', '/profile');
        $this->assertEquals(200, $response->getStatusCode());

        // Test two names
        $this->user->name = 'John Doe';
        $this->user->save();

        $response = $this->call('GET', '/profile');
        $this->assertEquals(200, $response->getStatusCode());

        // Test three names
        $this->user->name = 'John Marie Doe';
        $this->user->save();

        $response = $this->call('GET', '/profile');
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * Test industry dropdown population
     */
    public function testIndustryDropdownPopulation()
    {
        // Create additional industries with unique names
        Industry::create(['name' => 'Test Healthcare Industry']);
        Industry::create(['name' => 'Test Finance Industry']);

        $this->actingAs($this->user);

        $response = $this->call('GET', '/profile');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('Test Technology Industry', $response->getContent());
        $this->assertContains('Test Healthcare Industry', $response->getContent());
        $this->assertContains('Test Finance Industry', $response->getContent());
        $this->assertContains('Select Industry', $response->getContent());
    }

    /**
     * Test profile update with existing email
     */
    public function testProfileUpdateWithExistingEmail()
    {
        // Create another user with different email
        $otherUser = User::create([
            'username' => 'otheruser',
            'name' => 'Other User',
            'email' => 'other@example.com',
            'password' => bcrypt('password'),
            'client_id' => $this->client->id
        ]);

        $this->actingAs($this->user);

        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'other@example.com', // Email already exists
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'industry_id' => $this->industry->id
        ];

        $response = $this->call('POST', '/profile', $data);

        $this->assertEquals(302, $response->getStatusCode());
        
        // Should redirect back with errors
        $this->assertTrue($response->isRedirection());
    }

    /**
     * Test profile form validation rules
     */
    public function testProfileFormValidationRules()
    {
        $validator = Validator::make([], [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
            'industry_id' => 'sometimes|exists:industries,id'
        ]);

        // Test required fields
        $this->assertTrue($validator->fails());

        // Test valid data
        $validData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'industry_id' => $this->industry->id
        ];

        $validator = Validator::make($validData, [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
            'industry_id' => 'sometimes|exists:industries,id'
        ]);

        $this->assertFalse($validator->fails());
    }

    /**
     * Test profile completion flow
     */
    public function testProfileCompletionFlow()
    {
        $this->actingAs($this->user);

        // Step 1: Access profile page
        $response = $this->call('GET', '/profile');
        $this->assertEquals(200, $response->getStatusCode());

        // Step 2: Submit profile form
        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'industry_id' => $this->industry->id
        ];

        $response = $this->call('POST', '/profile', $data);
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertContains('/profile/research', $response->getTargetUrl());

        // Step 3: Verify user is redirected to research page
        $this->user = User::find($this->user->id);
        $this->assertTrue((bool)$this->user->completed_profile);
        $this->assertFalse((bool)$this->user->completed_research);
    }

    /**
     * Test profile page with client that doesn't require profile
     */
    public function testProfilePageWithClientNotRequiringProfile()
    {
        $this->client->require_profile = false;
        $this->client->require_research = false;
        $this->client->save();

        $this->actingAs($this->user);

        $response = $this->call('GET', '/profile');

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertContains('/assignments', $response->getTargetUrl());
    }

    /**
     * Test profile page with client that requires research but not profile
     */
    public function testProfilePageWithClientRequiringResearchOnly()
    {
        $this->client->require_profile = false;
        $this->client->require_research = true;
        $this->client->save();

        $this->user->completed_profile = true;
        $this->user->save();

        $this->actingAs($this->user);

        $response = $this->call('GET', '/profile');

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertContains('/profile/research', $response->getTargetUrl());
    }
}
