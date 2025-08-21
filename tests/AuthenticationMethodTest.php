<?php

use App\User;
use App\Http\Controllers\Auth\AuthController;
use Spatie\Permission\Models\Role;

class AuthenticationMethodTest extends TestCase
{

    protected $authController;
    protected $adminUser;

    public function setUp(): void
    {
        parent::setUp();
        
        $this->authController = new AuthController();
        
        // Create admin role and user
        $adminRole = Role::firstOrCreate(['name' => 'AOE Admin', 'guard_name' => 'web']);
        $this->adminUser = User::create([
            'username' => 'admin_' . uniqid(),
            'name' => 'Admin User',
            'email' => 'admin_' . uniqid() . '@test.com',
            'password' => bcrypt('password'),
        ]);
        $this->adminUser->assignRole($adminRole);
    }

    /**
     * Test that AuthController has all required methods
     */
    public function testAuthControllerHasRequiredMethods()
    {
        $requiredMethods = [
            'showLoginForm',
            'showRegistrationForm',
            'login',
            'register',
            'logout',
            'redirectPath',
            'sendFailedLoginResponse',
            'hasTooManyLoginAttempts',
            'clearLoginAttempts',
            'getLoginLockoutKey'
        ];

        foreach ($requiredMethods as $method) {
            $this->assertTrue(
                method_exists($this->authController, $method),
                "AuthController is missing required method: {$method}"
            );
        }
    }

    /**
     * Test that User model has all required compatibility methods
     */
    public function testUserModelHasCompatibilityMethods()
    {
        $requiredMethods = [
            'is',
            'has',
            'level',
            'isReseller',
            'isAdmin',
            'isClient',
            'hasRole',
            'assignRole'
        ];

        foreach ($requiredMethods as $method) {
            $this->assertTrue(
                method_exists($this->adminUser, $method),
                "User model is missing required method: {$method}"
            );
        }
    }

    /**
     * Test that redirectPath method returns correct value
     */
    public function testRedirectPathReturnsCorrectValue()
    {
        $redirectPath = $this->authController->redirectPath();
        $this->assertEquals('/dashboard', $redirectPath);
        $this->assertIsString($redirectPath);
    }

    /**
     * Test that login form is accessible
     */
    public function testLoginFormIsAccessible()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Welcome To AOE Science');
    }

    /**
     * Test that registration form is accessible
     */
    public function testRegistrationFormIsAccessible()
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
    }

    /**
     * Test that login works correctly
     */
    public function testLoginWorksCorrectly()
    {
        $response = $this->post('/login', [
            'email' => $this->adminUser->email,
            'password' => 'password'
        ]);

        $this->assertAuthenticated();
        $this->assertTrue(auth()->user()->hasRole('AOE Admin'));
    }

    /**
     * Test that logout works correctly
     */
    public function testLogoutWorksCorrectly()
    {
        $this->actingAs($this->adminUser);
        
        $this->assertAuthenticated();
        
        $response = $this->get('/auth/logout');
        
        $this->assertGuest();
    }

    /**
     * Test that failed login attempts are handled
     */
    public function testFailedLoginAttemptsAreHandled()
    {
        $response = $this->post('/login', [
            'email' => $this->adminUser->email,
            'password' => 'wrongpassword'
        ]);

        $this->assertGuest();
        $response->assertStatus(302); // Should redirect back with errors
    }

    /**
     * Test that User model compatibility methods work correctly
     */
    public function testUserCompatibilityMethodsWorkCorrectly()
    {
        // Test is() method
        $this->assertTrue($this->adminUser->is('admin'));
        $this->assertFalse($this->adminUser->is('reseller'));

        // Test has() method
        $this->assertTrue($this->adminUser->has('admin'));
        $this->assertFalse($this->adminUser->has('reseller'));

        // Test level() method
        $this->assertEquals(4, $this->adminUser->level());

        // Test specific role methods
        $this->assertTrue($this->adminUser->isAdmin());
        $this->assertFalse($this->adminUser->isReseller());
        $this->assertFalse($this->adminUser->isClient());

        // Test hasRole() method
        $this->assertTrue($this->adminUser->hasRole('AOE Admin'));
        $this->assertFalse($this->adminUser->hasRole('Reseller'));
    }

    /**
     * Test that role assignment works correctly
     */
    public function testRoleAssignmentWorksCorrectly()
    {
        $resellerRole = Role::firstOrCreate(['name' => 'Reseller', 'guard_name' => 'web']);
        
        $this->adminUser->assignRole($resellerRole);
        
        $this->assertTrue($this->adminUser->hasRole('Reseller'));
        $this->assertTrue($this->adminUser->is('reseller'));
    }

    /**
     * Test that multiple roles work correctly
     */
    public function testMultipleRolesWorkCorrectly()
    {
        $resellerRole = Role::firstOrCreate(['name' => 'Reseller', 'guard_name' => 'web']);
        
        $this->adminUser->assignRole($resellerRole);
        
        // Should have both roles
        $this->assertTrue($this->adminUser->hasRole('AOE Admin'));
        $this->assertTrue($this->adminUser->hasRole('Reseller'));
        
        // Should work with is() method for both
        $this->assertTrue($this->adminUser->is('admin'));
        $this->assertTrue($this->adminUser->is('reseller'));
    }

    /**
     * Test that role checking with pipe separator works
     */
    public function testRoleCheckingWithPipeSeparator()
    {
        $resellerRole = Role::firstOrCreate(['name' => 'Reseller', 'guard_name' => 'web']);
        $this->adminUser->assignRole($resellerRole);
        
        // Test is() method with pipe separator
        $this->assertTrue($this->adminUser->is('admin|reseller'));
        $this->assertTrue($this->adminUser->is('reseller|client'));
        $this->assertFalse($this->adminUser->is('client|user'));
    }

    /**
     * Test that authentication guards are properly configured
     */
    public function testAuthenticationGuardsAreConfigured()
    {
        $this->assertNotNull(config('auth.guards.web'));
        $this->assertNotNull(config('auth.providers.users'));
        $this->assertEquals('web', config('auth.defaults.guard'));
    }

    /**
     * Test that middleware is properly registered
     */
    public function testMiddlewareIsRegistered()
    {
        $kernel = app(\Illuminate\Contracts\Http\Kernel::class);
        
        // Test that our custom middleware is registered
        $this->assertTrue(
            in_array('level', array_keys($kernel->getRouteMiddleware())),
            'LevelMiddleware should be registered in route middleware'
        );
    }
}
