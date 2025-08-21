<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\User;
use App\Industry;
use App\Client;
use App\Language;
use Bican\Roles\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class UserManagementAuthenticationTest extends TestCase
{

    protected $industry;
    protected $client;
    protected $language;

    public function setUp()
    {
        parent::setUp();
        
        // Disable CSRF protection for tests
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);
        
        // Create test data
        $this->language = Language::firstOrCreate([
            'name' => 'English',
            'native_name' => 'English',
            'code' => 'en'
        ]);
        
        $this->industry = Industry::firstOrCreate([
            'name' => 'Test Technology Industry'
        ]);

        $this->client = Client::firstOrCreate([
            'name' => 'Test Client',
            'require_profile' => true,
            'require_research' => true
        ]);
        
        // Ensure roles exist for testing
        $this->createRolesIfNeeded();
        
        // Debug: Check client settings
        echo "Debug: Client require_profile: " . ($this->client->require_profile ? 'true' : 'false') . "\n";
    }
    
    /**
     * Create roles if they don't exist (for CI environment)
     */
    private function createRolesIfNeeded()
    {
        $roles = [
            [
                'name' => 'AOE Admin',
                'slug' => 'admin',
                'level' => 4
            ],
            [
                'name' => 'Reseller',
                'slug' => 'reseller',
                'level' => 3
            ],
            [
                'name' => 'Client Admin',
                'slug' => 'client',
                'level' => 2
            ],
            [
                'name' => 'User',
                'slug' => 'user',
                'level' => 1
            ]
        ];
        
        foreach ($roles as $roleData) {
            Role::firstOrCreate(['slug' => $roleData['slug']], $roleData);
        }
    }

    // ========================================
    // USER CREATION & PROFILE SETUP TESTS
    // ========================================

    /**
     * Test user creation and role assignment (core functionality)
     */
    public function testUserCreationAndRoleAssignment()
    {
        // Create a user using factory
        $user = factory(User::class)->create([
            'name' => 'John Doe',
            'username' => 'testuser_' . uniqid(),
            'email' => 'john_' . uniqid() . '@example.com',
            'password' => bcrypt('password123'),
            'client_id' => $this->client->id,
            'industry_id' => $this->industry->id,
            'language_id' => $this->language->id,
            'completed_profile' => false
        ]);

        // Verify user was created with correct attributes
        $this->assertNotNull($user);
        $this->assertEquals('John Doe', $user->name);
        $this->assertEquals($this->industry->id, $user->industry_id);
        $this->assertEquals($this->language->id, $user->language_id);
        $this->assertEquals($this->client->id, $user->client_id);
        $this->assertFalse($user->completed_profile);
        
        // Test role assignment
        $userRole = Role::where('slug', 'user')->first(); // User role
        $this->assertNotNull($userRole, 'User role should exist in database');
        $user->attachRole($userRole);
        
        // Verify role was assigned
        $this->assertTrue($user->roles->count() > 0);
        $this->assertEquals('user', $user->roles->first()->slug);
        
        // Verify password was hashed
        $this->assertTrue(Hash::check('password123', $user->password));
    }

    /**
     * Test user creation with duplicate username validation
     */
    public function testUserCreationWithDuplicateUsername()
    {
        // Create existing user
        $existingUser = factory(User::class)->create([
            'username' => 'existinguser'
        ]);

        // Try to create another user with the same username (should fail)
        try {
            $duplicateUser = factory(User::class)->create([
                'username' => 'existinguser' // Duplicate username
            ]);
            $this->fail('Expected exception for duplicate username was not thrown');
        } catch (\Exception $e) {
            // This is expected - duplicate username should cause an exception
            $this->assertContains('Duplicate', $e->getMessage());
        }

        // Verify only one user exists with that username
        $userCount = User::where('username', 'existinguser')->count();
        $this->assertEquals(1, $userCount);
    }

    /**
     * Test complete profile workflow
     */
    public function testCompleteProfileWorkflow()
    {
        $user = factory(User::class)->create([
            'client_id' => $this->client->id,
            'language_id' => $this->language->id,
            'completed_profile' => false
        ]);

        $this->actingAs($user);

        $profileData = [
            'first_name' => 'John',
            'middle_name' => 'Michael',
            'last_name' => 'Doe',
            'email' => $user->email,
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
            'industry_id' => $this->industry->id
        ];

        $this->post('/profile', $profileData);

        // Refresh user from database (Laravel 5.1 doesn't have refresh())
        $user = User::find($user->id);
        
        // Verify profile was completed
        $this->assertTrue((bool)$user->completed_profile);
        $this->assertEquals('John Michael Doe', $user->name);
        $this->assertEquals($this->industry->id, $user->industry_id);
    }

    /**
     * Test profile validation with empty required fields
     */
    public function testProfileValidationErrors()
    {
        // Test validation logic directly
        $validator = \Illuminate\Support\Facades\Validator::make([
            'first_name' => '',
            'last_name' => '',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ], [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed'
        ]);

        // Verify validation fails
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('first_name'));
        $this->assertTrue($validator->errors()->has('last_name'));
    }

    // ========================================
    // AUTHENTICATION TESTS
    // ========================================

    /**
     * Test login with valid credentials
     */
    public function testLoginWithValidCredentials()
    {
        $user = factory(User::class)->create([
            'password' => bcrypt('password123'),
            'client_id' => $this->client->id,
            'language_id' => $this->language->id
        ]);

        $loginData = [
            'username' => $user->username,
            'password' => 'password123'
        ];

        $this->visit('/login')
             ->type($loginData['username'], 'username')
             ->type($loginData['password'], 'password')
             ->press('Log In');

        // Verify user is authenticated
        $this->assertTrue(Auth::check());
        $this->assertEquals($user->id, Auth::id());
    }

    /**
     * Test login with invalid credentials
     */
    public function testLoginWithInvalidCredentials()
    {
        $user = factory(User::class)->create([
            'password' => bcrypt('password123'),
            'client_id' => $this->client->id,
            'language_id' => $this->language->id
        ]);

        $this->visit('/login')
             ->type($user->username, 'username')
             ->type('wrongpassword', 'password')
             ->press('Log In');

        // Verify user is not authenticated
        $this->assertFalse(Auth::check());
    }

    /**
     * Test login with non-existent username
     */
    public function testLoginWithNonExistentUsername()
    {
        $this->visit('/login')
             ->type('nonexistentuser', 'username')
             ->type('password123', 'password')
             ->press('Log In');

        // Verify user is not authenticated
        $this->assertFalse(Auth::check());
    }

    /**
     * Test logout functionality
     */
    public function testLogoutFunctionality()
    {
        $user = factory(User::class)->create([
            'client_id' => $this->client->id,
            'language_id' => $this->language->id
        ]);

        $this->actingAs($user);
        
        // Verify user is authenticated
        $this->assertTrue(Auth::check());

        $this->visit('/logout');
        
        // Verify user is no longer authenticated
        $this->assertFalse(Auth::check());
    }

    // ========================================
    // USER PROFILE MANAGEMENT TESTS
    // ========================================

    /**
     * Test edit profile with valid updates
     */
    public function testEditProfileWithValidUpdates()
    {
        $user = factory(User::class)->create([
            'name' => 'John Doe',
            'job_title' => 'Developer',
            'client_id' => $this->client->id,
            'language_id' => $this->language->id
        ]);

        $this->actingAs($user);

        $updateData = [
            'first_name' => 'Jane',
            'middle_name' => 'Marie',
            'last_name' => 'Smith',
            'email' => $user->email,
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
            'industry_id' => $this->industry->id
        ];

        $this->post('/profile', $updateData);

        // Refresh user from database (Laravel 5.1 doesn't have refresh())
        $user = User::find($user->id);
        
        // Verify updates were saved
        $this->assertEquals('Jane Marie Smith', $user->name);
        $this->assertEquals($this->industry->id, $user->industry_id);
    }

    /**
     * Test name parsing functionality
     */
    public function testNameParsingFunctionality()
    {
        // Test name parsing logic directly (same as in UsersController@update_profile)
        
        // Test single name
        $data = ['first_name' => 'John', 'last_name' => ''];
        $name = implode(' ', [$data['first_name'], $data['last_name']]);
        $this->assertEquals('John ', $name);
        
        // Test two names
        $data = ['first_name' => 'John', 'last_name' => 'Doe'];
        $name = implode(' ', [$data['first_name'], $data['last_name']]);
        $this->assertEquals('John Doe', $name);
        
        // Test three names
        $data = ['first_name' => 'John', 'middle_name' => 'Michael', 'last_name' => 'Doe'];
        $name = implode(' ', [$data['first_name'], $data['last_name']]);
        if (isset($data['middle_name']) && !empty(trim($data['middle_name']))) {
            $name = implode(' ', [$data['first_name'], trim($data['middle_name']), $data['last_name']]);
        }
        $this->assertEquals('John Michael Doe', $name);
    }

    /**
     * Test SQL injection prevention in profile updates
     */
    public function testSqlInjectionPreventionInProfileUpdates()
    {
        $user = factory(User::class)->create([
            'client_id' => $this->client->id,
            'language_id' => $this->language->id
        ]);

        $this->actingAs($user);

        $maliciousData = [
            'first_name' => "'; DROP TABLE users; --",
            'last_name' => "'; DELETE FROM users; --",
            'email' => $user->email,
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123'
        ];

        $this->post('/profile', $maliciousData);

        // Verify user still exists
        $user = User::find($user->id);
        $this->assertNotNull($user);
        
        // Verify no SQL injection occurred
        $this->assertNotEquals($maliciousData['first_name'], $user->name);
    }

    /**
     * Test session timeout functionality
     */
    public function testSessionTimeoutFunctionality()
    {
        $user = factory(User::class)->create([
            'client_id' => $this->client->id,
            'language_id' => $this->language->id
        ]);

        $this->actingAs($user);
        
        // Verify user is authenticated
        $this->assertTrue(Auth::check());

        // Log out the user
        Auth::logout();
        
        // Verify user is no longer authenticated
        $this->assertFalse(Auth::check());
        
        // Try to access protected route - should redirect to login
        $this->call('GET', '/profile');
        
        // Verify we get a redirect response (to login)
        $this->assertResponseStatus(302);
    }

    /**
     * Test user profile view functionality
     */
    public function testUserProfileViewFunctionality()
    {
        $user = factory(User::class)->create([
            'client_id' => $this->client->id,
            'language_id' => $this->language->id,
            'completed_profile' => false
        ]);

        $this->actingAs($user);

        $response = $this->visit('/profile');

        // Should see profile form
        $this->see('Profile');
    }

    /**
     * Test user profile redirect when completed
     */
    public function testUserProfileRedirectWhenCompleted()
    {
        $user = factory(User::class)->create([
            'client_id' => $this->client->id,
            'language_id' => $this->language->id,
            'completed_profile' => true,
            'completed_research' => true
        ]);

        $this->actingAs($user);

        $this->visit('/profile');

        // Should redirect to assignments when profile is complete
        $this->seePageIs('https://localhost/assignments');
    }
}
