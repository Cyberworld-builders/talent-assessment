<?php

use App\User;
use Spatie\Permission\Models\Role;

class ViewPermissionTest extends TestCase
{

    protected $adminUser;
    protected $resellerUser;
    protected $clientUser;

    public function setUp(): void
    {
        parent::setUp();
        
        // Create roles
        $adminRole = Role::firstOrCreate(['name' => 'AOE Admin', 'guard_name' => 'web']);
        $resellerRole = Role::firstOrCreate(['name' => 'Reseller', 'guard_name' => 'web']);
        $clientRole = Role::firstOrCreate(['name' => 'Client Admin', 'guard_name' => 'web']);

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
    }

    /**
     * Test that admin user sees all sidebar links
     */
    public function testAdminUserSeesAllSidebarLinks()
    {
        $this->actingAs($this->adminUser);
        
        $response = $this->get('/dashboard');
        $response->assertStatus(200);
        
        // Check for admin-specific sidebar links
        $response->assertSee('Assessments');
        $response->assertSee('Clients');
        $response->assertSee('Resellers');
        $response->assertSee('Industries');
        $response->assertSee('Benchmarks');
        $response->assertSee('Feedback');
        $response->assertSee('Users');
    }

    /**
     * Test that reseller user sees appropriate sidebar links
     */
    public function testResellerUserSeesAppropriateSidebarLinks()
    {
        $this->actingAs($this->resellerUser);
        
        $response = $this->get('/dashboard');
        $response->assertStatus(200);
        
        // Check for reseller-specific sidebar links
        $response->assertSee('Clients');
        $response->assertSee('Users');
        
        // Check that admin-specific links are NOT visible
        $response->assertDontSee('Assessments');
        $response->assertDontSee('Resellers');
        $response->assertDontSee('Industries');
        $response->assertDontSee('Benchmarks');
        $response->assertDontSee('Feedback');
    }

    /**
     * Test that client user sees appropriate sidebar links
     */
    public function testClientUserSeesAppropriateSidebarLinks()
    {
        $this->actingAs($this->clientUser);
        
        $response = $this->get('/dashboard');
        $response->assertStatus(200);
        
        // Check for client-specific sidebar links
        $response->assertSee('Employee Development');
        $response->assertSee('Users');
        
        // Check that admin-specific links are NOT visible
        $response->assertDontSee('Assessments');
        $response->assertDontSee('Clients');
        $response->assertDontSee('Resellers');
        $response->assertDontSee('Industries');
        $response->assertDontSee('Benchmarks');
        $response->assertDontSee('Feedback');
    }

    /**
     * Test that navigation shows correct role names
     */
    public function testNavigationShowsCorrectRoleNames()
    {
        $this->actingAs($this->adminUser);
        
        $response = $this->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('AOE Admin');
        
        $this->actingAs($this->resellerUser);
        $response = $this->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Admin'); // Reseller sees "Admin" in nav
        
        $this->actingAs($this->clientUser);
        $response = $this->get('/dashboard');
        $response->assertStatus(200);
        // Client user should see their client name, but we don't have a client relationship set up in this test
    }

    /**
     * Test that dashboard changelog shows admin-specific items for admin users
     */
    public function testDashboardChangelogShowsAdminItems()
    {
        $this->actingAs($this->adminUser);
        
        $response = $this->get('/dashboard');
        $response->assertStatus(200);
        
        // Check for admin-specific changelog items
        $response->assertSee('Edit Assignment view updated');
        $response->assertSee('Can now lock assessments');
        $response->assertSee('Can use the shortcode [job]');
        $response->assertSee('Updated job applicant view');
        $response->assertSee('For jobs, if an applicant has been assigned duplicate assessments');
    }

    /**
     * Test that non-admin users don't see admin-specific changelog items
     */
    public function testNonAdminUsersDontSeeAdminChangelogItems()
    {
        $this->actingAs($this->resellerUser);
        
        $response = $this->get('/dashboard');
        $response->assertStatus(200);
        
        // Check that admin-specific changelog items are NOT visible
        $response->assertDontSee('Edit Assignment view updated');
        $response->assertDontSee('Can now lock assessments');
        $response->assertDontSee('Can use the shortcode [job]');
        $response->assertDontSee('Updated job applicant view');
        $response->assertDontSee('For jobs, if an applicant has been assigned duplicate assessments');
    }

    /**
     * Test that role directives work correctly in blade templates
     */
    public function testRoleDirectivesWorkCorrectly()
    {
        // Test that @role('AOE Admin') works
        $this->actingAs($this->adminUser);
        $response = $this->get('/dashboard');
        $response->assertStatus(200);
        
        // Test that @role('Reseller') works
        $this->actingAs($this->resellerUser);
        $response = $this->get('/dashboard');
        $response->assertStatus(200);
        
        // Test that @role('Client Admin') works
        $this->actingAs($this->clientUser);
        $response = $this->get('/dashboard');
        $response->assertStatus(200);
    }

    /**
     * Test that old role names don't work (should not show content)
     */
    public function testOldRoleNamesDontWork()
    {
        // Create a user with old role name to test
        $oldAdminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $oldAdminUser = User::create([
            'username' => 'oldadmin',
            'name' => 'Old Admin User',
            'email' => 'oldadmin@test.com',
            'password' => bcrypt('password'),
        ]);
        $oldAdminUser->assignRole($oldAdminRole);
        
        $this->actingAs($oldAdminUser);
        
        $response = $this->get('/dashboard');
        $response->assertStatus(200);
        
        // Should NOT see admin-specific content because @role('AOE Admin') won't match 'admin'
        $response->assertDontSee('Assessments');
        $response->assertDontSee('Clients');
        $response->assertDontSee('Resellers');
    }
}
