<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\User;
use App\Industry;
use App\Client;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class ProfileControllerTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;
    protected $industry;
    protected $client;
    protected $controller;

    public function setUp()
    {
        parent::setUp();
        
        // Find existing industry or create a new one with unique name
        $this->industry = Industry::where('name', 'Test Technology Industry')->first();
        if (!$this->industry) {
            $this->industry = Industry::create([
                'name' => 'Test Technology Industry'
            ]);
        }

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
            'language_id' => 1,
            'completed_profile' => false,
            'completed_research' => false
        ]);

        $this->controller = new \App\Http\Controllers\UsersController();
    }

    /**
     * Test profile method with new user
     */
    public function testProfileMethodWithNewUser()
    {
        $this->actingAs($this->user);

        $response = $this->controller->profile();

        $this->assertInstanceOf('Illuminate\View\View', $response);
        $this->assertEquals('profile.index', $response->getName());
        
        $data = $response->getData();
        $this->assertEquals($this->user, $data['user']);
        $this->assertEquals('John', $data['first_name']);
        $this->assertEquals('', $data['middle_name']);
        $this->assertEquals('Doe', $data['last_name']);
    }

    /**
     * Test profile method with user who has no language
     */
    public function testProfileMethodWithNoLanguage()
    {
        $this->user->language_id = null;
        $this->user->save();

        $this->actingAs($this->user);

        $response = $this->controller->profile();

        $this->assertInstanceOf('Illuminate\Http\RedirectResponse', $response);
        $this->assertContains('/language', $response->getTargetUrl());
    }

    /**
     * Test profile method with completed profile
     */
    public function testProfileMethodWithCompletedProfile()
    {
        $this->user->completed_profile = true;
        $this->user->save();

        $this->actingAs($this->user);

        $response = $this->controller->profile();

        $this->assertInstanceOf('Illuminate\Http\RedirectResponse', $response);
        $this->assertContains('/profile/research', $response->getTargetUrl());
    }

    /**
     * Test profile method with all completed
     */
    public function testProfileMethodWithAllCompleted()
    {
        $this->user->completed_profile = true;
        $this->user->completed_research = true;
        $this->user->save();

        $this->actingAs($this->user);

        $response = $this->controller->profile();

        $this->assertInstanceOf('Illuminate\Http\RedirectResponse', $response);
        $this->assertContains('/assignments', $response->getTargetUrl());
    }

    /**
     * Test profile method with client not requiring profile
     */
    public function testProfileMethodWithClientNotRequiringProfile()
    {
        $this->client->require_profile = false;
        $this->client->save();

        $this->actingAs($this->user);

        $response = $this->controller->profile();

        $this->assertInstanceOf('Illuminate\Http\RedirectResponse', $response);
        $this->assertContains('/assignments', $response->getTargetUrl());
    }

    /**
     * Test profile method with client requiring research only
     */
    public function testProfileMethodWithClientRequiringResearchOnly()
    {
        $this->client->require_profile = false;
        $this->client->require_research = true;
        $this->client->save();

        $this->user->completed_profile = true;
        $this->user->save();

        $this->actingAs($this->user);

        $response = $this->controller->profile();

        $this->assertInstanceOf('Illuminate\Http\RedirectResponse', $response);
        $this->assertContains('/profile/research', $response->getTargetUrl());
    }

    /**
     * Test name parsing logic with single name
     */
    public function testNameParsingWithSingleName()
    {
        $this->user->name = 'John';
        $this->user->save();

        $this->actingAs($this->user);

        $response = $this->controller->profile();
        $data = $response->getData();
        
        $this->assertEquals('John', $data['first_name']);
        $this->assertEquals('', $data['middle_name']);
        $this->assertEquals('', $data['last_name']);
    }

    /**
     * Test name parsing logic with two names
     */
    public function testNameParsingWithTwoNames()
    {
        $this->user->name = 'John Doe';
        $this->user->save();

        $this->actingAs($this->user);

        $response = $this->controller->profile();
        $data = $response->getData();
        
        $this->assertEquals('John', $data['first_name']);
        $this->assertEquals('', $data['middle_name']);
        $this->assertEquals('Doe', $data['last_name']);
    }

    /**
     * Test name parsing logic with three names
     */
    public function testNameParsingWithThreeNames()
    {
        $this->user->name = 'John Marie Doe';
        $this->user->save();

        $this->actingAs($this->user);

        $response = $this->controller->profile();
        $data = $response->getData();
        
        $this->assertEquals('John', $data['first_name']);
        $this->assertEquals('Marie', $data['middle_name']);
        $this->assertEquals('Doe', $data['last_name']);
    }

    /**
     * Test update_profile method with valid data
     */
    public function testUpdateProfileWithValidData()
    {
        $this->actingAs($this->user);

        $request = new Request();
        $request->merge([
            'first_name' => 'Jane',
            'middle_name' => 'Marie',
            'last_name' => 'Smith',
            'email' => 'jane@example.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
            'industry_id' => $this->industry->id
        ]);

        $response = $this->controller->update_profile($request);

        $this->assertInstanceOf('Illuminate\Http\RedirectResponse', $response);
        $this->assertContains('/profile/research', $response->getTargetUrl());

        $this->user->refresh();
        $this->assertEquals('Jane Marie Smith', $this->user->name);
        $this->assertEquals('jane@example.com', $this->user->email);
        $this->assertEquals($this->industry->id, $this->user->industry_id);
        $this->assertTrue($this->user->completed_profile);
    }

    /**
     * Test update_profile method with validation errors
     */
    public function testUpdateProfileWithValidationErrors()
    {
        $this->actingAs($this->user);

        $request = new Request();
        $request->merge([
            'first_name' => '', // Required field missing
            'last_name' => '', // Required field missing
            'email' => 'invalid-email', // Invalid email
            'password' => 'short', // Too short
            'password_confirmation' => 'different', // Doesn't match
            'industry_id' => 999 // Non-existent industry
        ]);

        $response = $this->controller->update_profile($request);

        $this->assertInstanceOf('Illuminate\Http\RedirectResponse', $response);
        $this->assertTrue($response->isRedirection());

        $this->user->refresh();
        $this->assertFalse($this->user->completed_profile);
    }

    /**
     * Test update_profile method with optional middle name
     */
    public function testUpdateProfileWithOptionalMiddleName()
    {
        $this->actingAs($this->user);

        $request = new Request();
        $request->merge([
            'first_name' => 'Alice',
            'middle_name' => '', // Empty middle name
            'last_name' => 'Johnson',
            'email' => 'alice@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'industry_id' => $this->industry->id
        ]);

        $response = $this->controller->update_profile($request);

        $this->assertInstanceOf('Illuminate\Http\RedirectResponse', $response);

        $this->user->refresh();
        $this->assertEquals('Alice Johnson', $this->user->name); // Should not include empty middle name
    }

    /**
     * Test update_profile method with optional industry
     */
    public function testUpdateProfileWithOptionalIndustry()
    {
        $this->actingAs($this->user);

        $request = new Request();
        $request->merge([
            'first_name' => 'Bob',
            'last_name' => 'Wilson',
            'email' => 'bob@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'industry_id' => '' // Empty industry
        ]);

        $response = $this->controller->update_profile($request);

        $this->assertInstanceOf('Illuminate\Http\RedirectResponse', $response);

        $this->user->refresh();
        $this->assertNull($this->user->industry_id);
        $this->assertTrue($this->user->completed_profile);
    }

    /**
     * Test update_profile method with existing email
     */
    public function testUpdateProfileWithExistingEmail()
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

        $request = new Request();
        $request->merge([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'other@example.com', // Email already exists
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'industry_id' => $this->industry->id
        ]);

        $response = $this->controller->update_profile($request);

        $this->assertInstanceOf('Illuminate\Http\RedirectResponse', $response);
        $this->assertTrue($response->isRedirection());
    }

    /**
     * Test research method with new user
     */
    public function testResearchMethodWithNewUser()
    {
        $this->user->completed_profile = true;
        $this->user->save();

        $this->actingAs($this->user);

        $response = $this->controller->research();

        $this->assertInstanceOf('Illuminate\View\View', $response);
        $this->assertEquals('profile.research', $response->getName());
    }

    /**
     * Test research method with user who has no language
     */
    public function testResearchMethodWithNoLanguage()
    {
        $this->user->language_id = null;
        $this->user->save();

        $this->actingAs($this->user);

        $response = $this->controller->research();

        $this->assertInstanceOf('Illuminate\Http\RedirectResponse', $response);
        $this->assertContains('/language', $response->getTargetUrl());
    }

    /**
     * Test research method with all completed
     */
    public function testResearchMethodWithAllCompleted()
    {
        $this->user->completed_profile = true;
        $this->user->completed_research = true;
        $this->user->save();

        $this->actingAs($this->user);

        $response = $this->controller->research();

        $this->assertInstanceOf('Illuminate\Http\RedirectResponse', $response);
        $this->assertContains('/assignments', $response->getTargetUrl());
    }

    /**
     * Test validation rules for profile update
     */
    public function testProfileUpdateValidationRules()
    {
        $rules = [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
            'industry_id' => 'nullable|exists:industries,id'
        ];

        // Test empty data
        $validator = Validator::make([], $rules);
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

        $validator = Validator::make($validData, $rules);
        $this->assertFalse($validator->fails());

        // Test invalid email
        $invalidData = $validData;
        $invalidData['email'] = 'invalid-email';
        $validator = Validator::make($invalidData, $rules);
        $this->assertTrue($validator->fails());

        // Test short password
        $invalidData = $validData;
        $invalidData['password'] = 'short';
        $invalidData['password_confirmation'] = 'short';
        $validator = Validator::make($invalidData, $rules);
        $this->assertTrue($validator->fails());

        // Test non-matching passwords
        $invalidData = $validData;
        $invalidData['password_confirmation'] = 'different';
        $validator = Validator::make($invalidData, $rules);
        $this->assertTrue($validator->fails());

        // Test non-existent industry
        $invalidData = $validData;
        $invalidData['industry_id'] = 999;
        $validator = Validator::make($invalidData, $rules);
        $this->assertTrue($validator->fails());
    }
}
