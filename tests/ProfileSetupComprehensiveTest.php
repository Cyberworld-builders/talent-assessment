<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\User;
use App\Industry;
use App\Client;
use App\Language;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class ProfileSetupComprehensiveTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;
    protected $industry;
    protected $client;
    protected $language;

    public function setUp()
    {
        parent::setUp();
        
        // Disable CSRF protection for tests
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);
        
        // Ensure we have a language record
        $this->language = Language::first();
        if (!$this->language) {
            $this->language = Language::create([
                'name' => 'English',
                'native_name' => 'English',
                'code' => 'en'
            ]);
        }
        
        // Create test industry
        $this->industry = Industry::create([
            'name' => 'Test Technology Industry ' . uniqid()
        ]);

        // Create test client
        $this->client = Client::create([
            'name' => 'Test Client ' . uniqid(),
            'require_profile' => true,
            'require_research' => true
        ]);

        // Create test user
        $this->user = User::create([
            'username' => 'testuser_' . uniqid(),
            'name' => 'John Doe',
            'email' => 'john_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'client_id' => $this->client->id,
            'language_id' => $this->language->id,
            'completed_profile' => false,
            'completed_research' => false
        ]);
    }

    /**
     * Test complete profile setup flow from start to finish
     */
    public function testCompleteProfileSetupFlow()
    {
        $this->actingAs($this->user);

        // Step 1: Access profile page
        $response = $this->call('GET', '/profile');
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('Create Your Profile', $response->getContent());

        // Step 2: Submit profile form with all required fields
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

        // Step 3: Verify user data was updated correctly
        $this->user = User::find($this->user->id);
        $this->assertEquals('Jane Marie Smith', $this->user->name);
        $this->assertEquals('jane@example.com', $this->user->email);
        $this->assertEquals($this->industry->id, $this->user->industry_id);
        $this->assertTrue((bool)$this->user->completed_profile);
        $this->assertTrue(Hash::check('newpassword123', $this->user->password));
    }

    /**
     * Test profile setup with minimal required fields
     */
    public function testProfileSetupWithMinimalFields()
    {
        $this->actingAs($this->user);

        $data = [
            'first_name' => 'Bob',
            'last_name' => 'Wilson',
            'email' => 'bob@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
            // No middle_name, no industry_id
        ];

        $response = $this->call('POST', '/profile', $data);
        $this->assertEquals(302, $response->getStatusCode());

        $this->user = User::find($this->user->id);
        $this->assertEquals('Bob Wilson', $this->user->name);
        $this->assertEquals('bob@example.com', $this->user->email);
        $this->assertNull($this->user->industry_id);
        $this->assertTrue((bool)$this->user->completed_profile);
    }

    /**
     * Test profile setup with special characters in names
     */
    public function testProfileSetupWithSpecialCharacters()
    {
        $this->actingAs($this->user);

        $data = [
            'first_name' => 'José',
            'middle_name' => 'María',
            'last_name' => 'García-López',
            'email' => 'jose@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'industry_id' => $this->industry->id
        ];

        $response = $this->call('POST', '/profile', $data);
        $this->assertEquals(302, $response->getStatusCode());

        $this->user = User::find($this->user->id);
        $this->assertEquals('José María García-López', $this->user->name);
        $this->assertTrue((bool)$this->user->completed_profile);
    }

    /**
     * Test profile setup with very long names
     */
    public function testProfileSetupWithLongNames()
    {
        $this->actingAs($this->user);

        $longFirstName = str_repeat('A', 80);
        $longMiddleName = str_repeat('B', 80);
        $longLastName = str_repeat('C', 80);

        $data = [
            'first_name' => $longFirstName,
            'middle_name' => $longMiddleName,
            'last_name' => $longLastName,
            'email' => 'longnames@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'industry_id' => $this->industry->id
        ];

        $response = $this->call('POST', '/profile', $data);
        $this->assertEquals(302, $response->getStatusCode());

        $this->user = User::find($this->user->id);
        $expectedName = $longFirstName . ' ' . $longMiddleName . ' ' . $longLastName;
        $this->assertEquals($expectedName, $this->user->name);
        $this->assertTrue((bool)$this->user->completed_profile);
    }

    /**
     * Test profile setup with empty middle name
     */
    public function testProfileSetupWithEmptyMiddleName()
    {
        $this->actingAs($this->user);

        $data = [
            'first_name' => 'Alice',
            'middle_name' => '',
            'last_name' => 'Johnson',
            'email' => 'alice@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'industry_id' => $this->industry->id
        ];

        $response = $this->call('POST', '/profile', $data);
        $this->assertEquals(302, $response->getStatusCode());

        $this->user = User::find($this->user->id);
        $this->assertEquals('Alice Johnson', $this->user->name);
        $this->assertTrue((bool)$this->user->completed_profile);
    }

    /**
     * Test profile setup with missing middle name field
     */
    public function testProfileSetupWithMissingMiddleNameField()
    {
        $this->actingAs($this->user);

        $data = [
            'first_name' => 'Charlie',
            'last_name' => 'Brown',
            'email' => 'charlie@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'industry_id' => $this->industry->id
            // No middle_name field at all
        ];

        $response = $this->call('POST', '/profile', $data);
        $this->assertEquals(302, $response->getStatusCode());

        $this->user = User::find($this->user->id);
        $this->assertEquals('Charlie Brown', $this->user->name);
        $this->assertTrue((bool)$this->user->completed_profile);
    }

    /**
     * Test profile setup with empty industry selection
     */
    public function testProfileSetupWithEmptyIndustry()
    {
        $this->actingAs($this->user);

        $data = [
            'first_name' => 'David',
            'last_name' => 'Miller',
            'email' => 'david@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'industry_id' => ''
        ];

        $response = $this->call('POST', '/profile', $data);
        $this->assertEquals(302, $response->getStatusCode());

        $this->user = User::find($this->user->id);
        $this->assertNull($this->user->industry_id);
        $this->assertTrue((bool)$this->user->completed_profile);
    }

    /**
     * Test profile setup with missing industry field
     */
    public function testProfileSetupWithMissingIndustryField()
    {
        $this->actingAs($this->user);

        $data = [
            'first_name' => 'Eva',
            'last_name' => 'Davis',
            'email' => 'eva@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
            // No industry_id field at all
        ];

        $response = $this->call('POST', '/profile', $data);
        $this->assertEquals(302, $response->getStatusCode());

        $this->user = User::find($this->user->id);
        $this->assertNull($this->user->industry_id);
        $this->assertTrue((bool)$this->user->completed_profile);
    }

    /**
     * Test validation errors for missing required fields
     */
    public function testValidationErrorsForMissingRequiredFields()
    {
        $this->actingAs($this->user);

        $data = [
            'first_name' => '',
            'last_name' => '',
            'email' => '',
            'password' => '',
            'password_confirmation' => ''
        ];

        $response = $this->call('POST', '/profile', $data);
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertTrue($response->isRedirection());

        $this->user = User::find($this->user->id);
        $this->assertFalse((bool)$this->user->completed_profile);
    }

    /**
     * Test validation errors for invalid email
     */
    public function testValidationErrorsForInvalidEmail()
    {
        $this->actingAs($this->user);

        $data = [
            'first_name' => 'Frank',
            'last_name' => 'Wilson',
            'email' => 'invalid-email',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'industry_id' => $this->industry->id
        ];

        $response = $this->call('POST', '/profile', $data);
        $this->assertEquals(302, $response->getStatusCode());

        $this->user = User::find($this->user->id);
        $this->assertFalse((bool)$this->user->completed_profile);
    }

    /**
     * Test validation errors for password mismatch
     */
    public function testValidationErrorsForPasswordMismatch()
    {
        $this->actingAs($this->user);

        $data = [
            'first_name' => 'Grace',
            'last_name' => 'Taylor',
            'email' => 'grace@example.com',
            'password' => 'password123',
            'password_confirmation' => 'differentpassword',
            'industry_id' => $this->industry->id
        ];

        $response = $this->call('POST', '/profile', $data);
        $this->assertEquals(302, $response->getStatusCode());

        $this->user = User::find($this->user->id);
        $this->assertFalse((bool)$this->user->completed_profile);
    }

    /**
     * Test validation errors for short password
     */
    public function testValidationErrorsForShortPassword()
    {
        $this->actingAs($this->user);

        $data = [
            'first_name' => 'Henry',
            'last_name' => 'Anderson',
            'email' => 'henry@example.com',
            'password' => '123',
            'password_confirmation' => '123',
            'industry_id' => $this->industry->id
        ];

        $response = $this->call('POST', '/profile', $data);
        $this->assertEquals(302, $response->getStatusCode());

        $this->user = User::find($this->user->id);
        $this->assertFalse((bool)$this->user->completed_profile);
    }

    /**
     * Test validation errors for non-existent industry
     */
    public function testValidationErrorsForNonExistentIndustry()
    {
        $this->actingAs($this->user);

        $data = [
            'first_name' => 'Iris',
            'last_name' => 'Thomas',
            'email' => 'iris@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'industry_id' => 99999 // Non-existent industry ID
        ];

        $response = $this->call('POST', '/profile', $data);
        $this->assertEquals(302, $response->getStatusCode());

        $this->user = User::find($this->user->id);
        $this->assertFalse((bool)$this->user->completed_profile);
    }

    /**
     * Test profile page access with different user states
     */
    public function testProfilePageAccessWithDifferentUserStates()
    {
        // Test with user who has no language
        $this->user->language_id = null;
        $this->user->save();

        $this->actingAs($this->user);
        $response = $this->call('GET', '/profile');
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertContains('/language', $response->getTargetUrl());

        // Test with user who has completed profile
        $this->user->language_id = $this->language->id;
        $this->user->completed_profile = true;
        $this->user->save();

        $response = $this->call('GET', '/profile');
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertContains('/profile/research', $response->getTargetUrl());

        // Test with user who has completed both profile and research
        $this->user->completed_research = true;
        $this->user->save();

        $response = $this->call('GET', '/profile');
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertContains('/assignments', $response->getTargetUrl());
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
     * Test industry dropdown population
     */
    public function testIndustryDropdownPopulation()
    {
        // Create additional industries
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
     * Test profile form includes all required fields
     */
    public function testProfileFormIncludesAllRequiredFields()
    {
        $this->actingAs($this->user);
        $response = $this->call('GET', '/profile');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('name="first_name"', $response->getContent());
        $this->assertContains('name="middle_name"', $response->getContent());
        $this->assertContains('name="last_name"', $response->getContent());
        $this->assertContains('name="email"', $response->getContent());
        $this->assertContains('name="password"', $response->getContent());
        $this->assertContains('name="password_confirmation"', $response->getContent());
        $this->assertContains('name="industry_id"', $response->getContent());
    }

    /**
     * Test profile form includes CSRF protection
     */
    public function testProfileFormIncludesCSRFProtection()
    {
        $this->actingAs($this->user);
        $response = $this->call('GET', '/profile');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('_token', $response->getContent());
    }

    /**
     * Test profile form displays current user data
     */
    public function testProfileFormDisplaysCurrentUserData()
    {
        $this->actingAs($this->user);
        $response = $this->call('GET', '/profile');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains($this->user->email, $response->getContent());
    }

    /**
     * Test profile completion marks user as completed
     */
    public function testProfileCompletionMarksUserAsCompleted()
    {
        $this->actingAs($this->user);

        $data = [
            'first_name' => 'Jack',
            'last_name' => 'Jackson',
            'email' => 'jack@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'industry_id' => $this->industry->id
        ];

        $response = $this->call('POST', '/profile', $data);
        $this->assertEquals(302, $response->getStatusCode());

        $this->user = User::find($this->user->id);
        $this->assertTrue((bool)$this->user->completed_profile);
        $this->assertFalse((bool)$this->user->completed_research);
    }

    /**
     * Test profile update redirects to research page
     */
    public function testProfileUpdateRedirectsToResearchPage()
    {
        $this->actingAs($this->user);

        $data = [
            'first_name' => 'Kate',
            'last_name' => 'White',
            'email' => 'kate@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'industry_id' => $this->industry->id
        ];

        $response = $this->call('POST', '/profile', $data);
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertContains('/profile/research', $response->getTargetUrl());
    }
}
