<?php

use App\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AuthenticationPermissionTest extends TestCase
{

    protected $adminUser;
    protected $resellerUser;
    protected $clientUser;
    protected $regularUser;

    public function setUp(): void
    {
        parent::setUp();
        
        // Create roles
        $adminRole = Role::firstOrCreate(['name' => 'AOE Admin', 'guard_name' => 'web']);
        $resellerRole = Role::firstOrCreate(['name' => 'Reseller', 'guard_name' => 'web']);
        $clientRole = Role::firstOrCreate(['name' => 'Client Admin', 'guard_name' => 'web']);
        $userRole = Role::firstOrCreate(['name' => 'User', 'guard_name' => 'web']);

        // Create users with unique emails
        $this->adminUser = User::create([
            'username' => 'admin_' . uniqid(),
            'name' => 'Admin User',
            'email' => 'admin_' . uniqid() . '@test.com',
            'password' => bcrypt('password'),
        ]);
        $this->adminUser->assignRole($adminRole);

        $this->resellerUser = User::create([
            'username' => 'reseller_' . uniqid(),
            'name' => 'Reseller User',
            'email' => 'reseller_' . uniqid() . '@test.com',
            'password' => bcrypt('password'),
        ]);
        $this->resellerUser->assignRole($resellerRole);

        $this->clientUser = User::create([
            'username' => 'client_' . uniqid(),
            'name' => 'Client User',
            'email' => 'client_' . uniqid() . '@test.com',
            'password' => bcrypt('password'),
        ]);
        $this->clientUser->assignRole($clientRole);

        $this->regularUser = User::create([
            'username' => 'user_' . uniqid(),
            'name' => 'Regular User',
            'email' => 'user_' . uniqid() . '@test.com',
            'password' => bcrypt('password'),
        ]);
        $this->regularUser->assignRole($userRole);
    }

    /**
     * Test that roles are properly created and assigned
     */
    public function testRolesAreProperlyCreated()
    {
        $this->assertTrue($this->adminUser->hasRole('AOE Admin'));
        $this->assertTrue($this->resellerUser->hasRole('Reseller'));
        $this->assertTrue($this->clientUser->hasRole('Client Admin'));
        $this->assertTrue($this->regularUser->hasRole('User'));
    }

    /**
     * Test compatibility methods for old bican/roles methods
     */
    public function testCompatibilityMethods()
    {
        // Test is() method
        $this->assertTrue($this->adminUser->is('admin'));
        $this->assertTrue($this->resellerUser->is('reseller'));
        $this->assertTrue($this->clientUser->is('client'));
        $this->assertFalse($this->adminUser->is('reseller'));

        // Test has() method
        $this->assertTrue($this->adminUser->has('admin'));
        $this->assertTrue($this->resellerUser->has('reseller'));
        $this->assertTrue($this->clientUser->has('client'));
        $this->assertFalse($this->adminUser->has('reseller'));

        // Test level() method
        $this->assertEquals(4, $this->adminUser->level());
        $this->assertEquals(3, $this->resellerUser->level());
        $this->assertEquals(2, $this->clientUser->level());
        $this->assertEquals(1, $this->regularUser->level());

        // Test specific role methods
        $this->assertTrue($this->adminUser->isAdmin());
        $this->assertTrue($this->resellerUser->isReseller());
        $this->assertTrue($this->clientUser->isClient());
    }

    /**
     * Test that users can authenticate properly
     */
    public function testUserAuthentication()
    {
        $response = $this->post('/login', [
            'email' => $this->adminUser->email,
            'password' => 'password'
        ]);

        $this->assertAuthenticated();
        $this->assertTrue(auth()->user()->hasRole('AOE Admin'));
    }

    /**
     * Test that role checks work in blade templates
     */
    public function testRoleChecksInBladeTemplates()
    {
        $this->actingAs($this->adminUser);

        // Test that admin user can see admin-specific content
        $response = $this->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('AOE Admin');
    }

    /**
     * Test that users without proper roles are denied access
     */
    public function testAccessDeniedForInsufficientRoles()
    {
        $this->actingAs($this->regularUser);

        // Test that regular user cannot access admin routes
        $response = $this->get('/dashboard/assessments');
        $response->assertStatus(302); // Should redirect
    }

    /**
     * Test that the redirectPath method exists in AuthController
     */
    public function testAuthControllerRedirectPathMethod()
    {
        $authController = new \App\Http\Controllers\Auth\AuthController();
        
        // Test that the method exists
        $this->assertTrue(method_exists($authController, 'redirectPath'));
        
        // Use reflection to call the protected method
        $reflection = new \ReflectionClass($authController);
        $method = $reflection->getMethod('redirectPath');
        $method->setAccessible(true);
        
        $result = $method->invoke($authController);
        $this->assertIsString($result);
        $this->assertEquals('/dashboard', $result);
    }

    /**
     * Test that the LevelMiddleware works correctly
     */
    public function testLevelMiddleware()
    {
        $middleware = new \App\Http\Middleware\LevelMiddleware();
        
        // Test with admin user (level 4)
        $this->actingAs($this->adminUser);
        $request = \Illuminate\Http\Request::create('/test', 'GET');
        
        $response = $middleware->handle($request, function($req) {
            return response('OK');
        }, 2); // Require level 2
        
        $this->assertEquals('OK', $response->getContent());
    }

    /**
     * Test that users with insufficient level are redirected
     */
    public function testLevelMiddlewareInsufficientLevel()
    {
        $middleware = new \App\Http\Middleware\LevelMiddleware();
        
        // Test with regular user (level 1)
        $this->actingAs($this->regularUser);
        $request = \Illuminate\Http\Request::create('/test', 'GET');
        
        $response = $middleware->handle($request, function($req) {
            return response('OK');
        }, 3); // Require level 3
        
        $this->assertEquals(302, $response->getStatusCode()); // Should redirect
    }

    /**
     * Test that unauthenticated users are redirected by LevelMiddleware
     */
    public function testLevelMiddlewareUnauthenticated()
    {
        $middleware = new \App\Http\Middleware\LevelMiddleware();
        
        $request = \Illuminate\Http\Request::create('/test', 'GET');
        
        $response = $middleware->handle($request, function($req) {
            return response('OK');
        }, 1); // Require level 1
        
        $this->assertEquals(302, $response->getStatusCode()); // Should redirect
    }
}
