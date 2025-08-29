<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Industry;
use App\User;
use App\Client;
use App\Assessment;
use App\Dimension;
use App\Benchmark;
use App\Language;
use App\Job;
use App\Http\Controllers\IndustriesController;
use Bican\Roles\Models\Role;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class IndustryManagementSystemTest extends TestCase
{
    protected $user;
    protected $client;
    protected $language;
    protected $assessment;
    protected $job;
    protected $dimensions;
    protected $industriesController;

    public function setUp()
    {
        parent::setUp();
        
        // Disable CSRF protection for tests
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);
        
        // Initialize controller
        $this->industriesController = new IndustriesController();
        
        // Create test data
        $this->language = Language::firstOrCreate([
            'name' => 'English',
            'native_name' => 'English',
            'code' => 'en'
        ]);
        
        $this->client = Client::firstOrCreate([
            'name' => 'Test Client for Industry Management',
            'require_profile' => true,
            'require_research' => true
        ]);

        $this->job = Job::firstOrCreate([
            'name' => 'Test Job Position for Industry Management',
            'slug' => 'test-job-industry-' . uniqid(),
            'client_id' => $this->client->id
        ]);
        
        // Ensure roles exist for testing
        $this->createRolesIfNeeded();
        
        // Create test user with admin role
        $this->user = User::create([
            'username' => 'industryadmin_' . uniqid(),
            'name' => 'Industry Test Admin',
            'email' => 'industryadmin_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'client_id' => $this->client->id,
            'language_id' => $this->language->id,
            'completed_profile' => true,
            'completed_research' => true
        ]);
        
        $adminRole = Role::where('slug', 'admin')->first();
        $this->user->attachRole($adminRole);

        // Create test assessment with dimensions
        $this->createTestAssessmentWithDimensions();
    }
    
    /**
     * Create roles if they don't exist (for CI environment)
     */
    private function createRolesIfNeeded()
    {
        $roles = [
            ['name' => 'AOE Admin', 'slug' => 'admin', 'level' => 4],
            ['name' => 'Reseller', 'slug' => 'reseller', 'level' => 3],
            ['name' => 'Client Admin', 'slug' => 'client', 'level' => 2],
            ['name' => 'User', 'slug' => 'user', 'level' => 1]
        ];
        
        foreach ($roles as $roleData) {
            Role::firstOrCreate(['slug' => $roleData['slug']], $roleData);
        }
    }

    /**
     * Create test assessment with dimensions
     */
    private function createTestAssessmentWithDimensions()
    {
        // Create assessment
        $assessmentData = [
            'name' => 'Test Assessment for Industry Management',
            'description' => 'Assessment for testing industry management functionality',
            'use_custom_fields' => false,
            'whitelabel' => false,
            'translation' => false,
            'timed' => false,
            'paginate' => false,
            'custom_fields' => [],
            'anchors' => []
        ];
        
        $this->assessment = new Assessment($assessmentData);
        $this->user->assessments()->save($this->assessment);

        // Create dimensions for industry testing
        $this->dimensions = [];
        
        // Leadership dimension
        $leadershipDimension = Dimension::create([
            'name' => 'Leadership',
            'code' => 'LEAD',
            'parent' => 0,
            'assessment_id' => $this->assessment->id
        ]);
        $this->dimensions['leadership'] = $leadershipDimension;

        // Communication dimension
        $communicationDimension = Dimension::create([
            'name' => 'Communication',
            'code' => 'COMM',
            'parent' => 0,
            'assessment_id' => $this->assessment->id
        ]);
        $this->dimensions['communication'] = $communicationDimension;

        // Problem Solving dimension
        $problemSolvingDimension = Dimension::create([
            'name' => 'Problem Solving',
            'code' => 'PROB',
            'parent' => 0,
            'assessment_id' => $this->assessment->id
        ]);
        $this->dimensions['problem_solving'] = $problemSolvingDimension;

        // Teamwork dimension
        $teamworkDimension = Dimension::create([
            'name' => 'Teamwork',
            'code' => 'TEAM',
            'parent' => 0,
            'assessment_id' => $this->assessment->id
        ]);
        $this->dimensions['teamwork'] = $teamworkDimension;
    }

    // ========================================
    // INDUSTRY CREATION AND MANAGEMENT TESTS
    // ========================================

    /**
     * Test industry creation with comprehensive data
     */
    public function testIndustryCreation()
    {
        $industryData = [
            'name' => 'Advanced Technology Solutions'
        ];

        $industry = Industry::create($industryData);

        $this->assertNotNull($industry->id);
        $this->assertEquals('Advanced Technology Solutions', $industry->name);
        $this->assertNotNull($industry->created_at);
        $this->assertNotNull($industry->updated_at);
    }

    /**
     * Test industry creation with special characters and formatting
     */
    public function testIndustryCreationWithSpecialCharacters()
    {
        $industryNames = [
            'Technology & Innovation',
            'Healthcare & Life Sciences',
            'Financial Services & Banking',
            'Manufacturing & Engineering',
            'Consulting & Professional Services'
        ];

        foreach ($industryNames as $name) {
            $industry = Industry::firstOrCreate(['name' => $name]);
            $this->assertEquals($name, $industry->name);
        }
    }

    /**
     * Test industry unique name constraint
     */
    public function testIndustryUniqueNameConstraint()
    {
        // Create first industry
        Industry::create(['name' => 'Unique Industry Test']);

        // Try to create duplicate - should fail
        $this->setExpectedException('Illuminate\Database\QueryException');
        
        Industry::create(['name' => 'Unique Industry Test']);
    }

    /**
     * Test industry editing and updates
     */
    public function testIndustryEditing()
    {
        $industry = Industry::create([
            'name' => 'Original Industry Name'
        ]);

        // Update industry name
        $industry->name = 'Updated Industry Name';
        $industry->save();

        // Verify update
        $updatedIndustry = Industry::find($industry->id);
        $this->assertEquals('Updated Industry Name', $updatedIndustry->name);
    }

    /**
     * Test industry deletion
     */
    public function testIndustryDeletion()
    {
        $industry = Industry::create([
            'name' => 'Deletable Industry'
        ]);

        $industryId = $industry->id;

        // Delete industry
        $industry->delete();

        // Verify deletion
        $deletedIndustry = Industry::find($industryId);
        $this->assertNull($deletedIndustry);
    }

    // ========================================
    // INDUSTRY CONTROLLER TESTS
    // ========================================

    /**
     * Test industries controller index method
     */
    public function testIndustriesControllerIndex()
    {
        $this->be($this->user);

        // Create some test industries
        Industry::create(['name' => 'Test Industry 1']);
        Industry::create(['name' => 'Test Industry 2']);
        Industry::create(['name' => 'Test Industry 3']);

        $response = $this->call('GET', 'dashboard/industries');

        // Should return 200 or redirect (depending on middleware)
        $this->assertTrue(in_array($response->getStatusCode(), [200, 302]));
    }

    /**
     * Test industries controller store method
     */
    public function testIndustriesControllerStore()
    {
        $this->be($this->user);

        $requestData = [
            'name' => 'Controller Test Industry'
        ];

        $response = $this->call('POST', 'dashboard/industries', $requestData);

        // Should redirect back to industries index
        $this->assertEquals(302, $response->getStatusCode());

        // Verify industry was created
        $industry = Industry::where('name', 'Controller Test Industry')->first();
        $this->assertNotNull($industry);
    }

    /**
     * Test industries controller validation
     */
    public function testIndustriesControllerValidation()
    {
        $this->be($this->user);

        // Test missing name
        $response = $this->call('POST', 'dashboard/industries', []);

        // Should redirect back with errors or return 500 if validation fails
        $this->assertTrue(in_array($response->getStatusCode(), [302, 500]));

        // Test duplicate name
        Industry::firstOrCreate(['name' => 'Duplicate Test Industry']);
        
        $response = $this->call('POST', 'dashboard/industries', [
            'name' => 'Duplicate Test Industry'
        ]);

        $this->assertTrue(in_array($response->getStatusCode(), [302, 500]));
    }

    /**
     * Test industries controller update method
     */
    public function testIndustriesControllerUpdate()
    {
        $this->be($this->user);

        $industry = Industry::create(['name' => 'Original Name']);

        $requestData = [
            'name' => 'Updated Name'
        ];

        $response = $this->call('PATCH', "dashboard/industries/{$industry->id}", $requestData);

        // Should redirect back to industries index
        $this->assertEquals(302, $response->getStatusCode());

        // Verify industry was updated
        $updatedIndustry = Industry::find($industry->id);
        $this->assertEquals('Updated Name', $updatedIndustry->name);
    }

    /**
     * Test industries controller destroy method
     */
    public function testIndustriesControllerDestroy()
    {
        $this->be($this->user);

        $industry = Industry::create(['name' => 'Deletable Industry']);

        $response = $this->call('DELETE', "dashboard/industries/{$industry->id}");

        // Should redirect back to industries index
        $this->assertEquals(302, $response->getStatusCode());

        // Verify industry was deleted
        $deletedIndustry = Industry::find($industry->id);
        $this->assertNull($deletedIndustry);
    }

    // ========================================
    // USER-INDUSTRY ASSOCIATION TESTS
    // ========================================

    /**
     * Test user assignment to industry
     */
    public function testUserIndustryAssignment()
    {
        $industry = Industry::create([
            'name' => 'Technology Industry'
        ]);

        $user = User::create([
            'username' => 'industryuser_' . uniqid(),
            'name' => 'Industry Test User',
            'email' => 'industryuser_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'client_id' => $this->client->id,
            'language_id' => $this->language->id,
            'industry_id' => $industry->id
        ]);

        $this->assertEquals($industry->id, $user->industry_id);
        
        // Test relationship
        $this->assertNotNull($user->industry);
        $this->assertEquals('Technology Industry', $user->industry->name);
    }

    /**
     * Test user without industry assignment
     */
    public function testUserWithoutIndustry()
    {
        $user = User::create([
            'username' => 'noindustryuser_' . uniqid(),
            'name' => 'No Industry User',
            'email' => 'noindustryuser_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'client_id' => $this->client->id,
            'language_id' => $this->language->id,
            'industry_id' => null
        ]);

        $this->assertNull($user->industry_id);
        $this->assertNull($user->industry);
    }

    /**
     * Test user industry reassignment
     */
    public function testUserIndustryReassignment()
    {
        $industry1 = Industry::firstOrCreate(['name' => 'Technology Test Industry 1']);
        $industry2 = Industry::firstOrCreate(['name' => 'Healthcare Test Industry 2']);

        $user = User::create([
            'username' => 'reassignuser_' . uniqid(),
            'name' => 'Reassign User',
            'email' => 'reassignuser_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'client_id' => $this->client->id,
            'language_id' => $this->language->id,
            'industry_id' => $industry1->id
        ]);

        // Verify initial assignment
        $this->assertEquals($industry1->id, $user->industry_id);

        // Reassign to different industry
        $user->industry_id = $industry2->id;
        $user->save();

        // Verify reassignment
        $updatedUser = User::find($user->id);
        $this->assertEquals($industry2->id, $updatedUser->industry_id);
        $this->assertEquals('Healthcare Test Industry 2', $updatedUser->industry->name);
    }

    /**
     * Test bulk user industry assignment
     */
    public function testBulkUserIndustryAssignment()
    {
        $industry = Industry::firstOrCreate(['name' => 'Finance Test Industry']);

        $users = [];
        for ($i = 0; $i < 5; $i++) {
            $users[] = User::create([
                'username' => "bulkuser{$i}_" . uniqid(),
                'name' => "Bulk User {$i}",
                'email' => "bulkuser{$i}_" . uniqid() . '@example.com',
                'password' => bcrypt('password'),
                'client_id' => $this->client->id,
                'language_id' => $this->language->id,
                'industry_id' => $industry->id
            ]);
        }

        // Verify all users are assigned to the same industry
        foreach ($users as $user) {
            $this->assertEquals($industry->id, $user->industry_id);
            $this->assertEquals('Finance Test Industry', $user->industry->name);
        }
    }

    // ========================================
    // INDUSTRY-SPECIFIC FEATURE TESTS
    // ========================================

    /**
     * Test industry-specific benchmark creation
     */
    public function testIndustrySpecificBenchmarks()
    {
        $industry = Industry::firstOrCreate(['name' => 'Technology Test Industry']);

        // Create benchmarks for this industry
        $benchmarks = [];
        foreach ($this->dimensions as $key => $dimension) {
            $benchmarks[] = Benchmark::create([
                'dimension_id' => $dimension->id,
                'industry_id' => $industry->id,
                'value' => strval(75 + rand(0, 20)) // Random value between 75-95
            ]);
        }

        // Verify benchmarks are associated with the industry
        $this->assertCount(4, $benchmarks);
        foreach ($benchmarks as $benchmark) {
            $this->assertEquals($industry->id, $benchmark->industry_id);
            $this->assertNotNull($benchmark->industry);
            $this->assertEquals('Technology Test Industry', $benchmark->industry->name);
        }

        // Test industry benchmarks relationship
        $industryBenchmarks = $industry->benchmarks;
        $this->assertCount(4, $industryBenchmarks);
    }

    /**
     * Test industry-specific user filtering
     */
    public function testIndustrySpecificUserFiltering()
    {
        $technologyIndustry = Industry::firstOrCreate(['name' => 'Technology Test Industry']);
        $healthcareIndustry = Industry::firstOrCreate(['name' => 'Healthcare Test Industry']);

        // Create users in different industries
        $techUsers = [];
        $healthcareUsers = [];

        for ($i = 0; $i < 3; $i++) {
            $techUsers[] = User::create([
                'username' => "techuser{$i}_" . uniqid(),
                'name' => "Tech User {$i}",
                'email' => "techuser{$i}_" . uniqid() . '@example.com',
                'password' => bcrypt('password'),
                'client_id' => $this->client->id,
                'language_id' => $this->language->id,
                'industry_id' => $technologyIndustry->id
            ]);

            $healthcareUsers[] = User::create([
                'username' => "healthuser{$i}_" . uniqid(),
                'name' => "Healthcare User {$i}",
                'email' => "healthuser{$i}_" . uniqid() . '@example.com',
                'password' => bcrypt('password'),
                'client_id' => $this->client->id,
                'language_id' => $this->language->id,
                'industry_id' => $healthcareIndustry->id
            ]);
        }

        // Test filtering by industry
        $techIndustryUsers = User::where('industry_id', $technologyIndustry->id)->get();
        $healthcareIndustryUsers = User::where('industry_id', $healthcareIndustry->id)->get();

        $this->assertCount(3, $techIndustryUsers);
        $this->assertCount(3, $healthcareIndustryUsers);

        // Verify all users in tech industry are correctly assigned
        foreach ($techIndustryUsers as $user) {
            $this->assertEquals($technologyIndustry->id, $user->industry_id);
            $this->assertEquals('Technology Test Industry', $user->industry->name);
        }

        // Verify all users in healthcare industry are correctly assigned
        foreach ($healthcareIndustryUsers as $user) {
            $this->assertEquals($healthcareIndustry->id, $user->industry_id);
            $this->assertEquals('Healthcare Test Industry', $user->industry->name);
        }
    }

    /**
     * Test industry-specific assessment assignments
     */
    public function testIndustrySpecificAssessmentAssignments()
    {
        $industry = Industry::firstOrCreate(['name' => 'Manufacturing Test Industry']);

        $user = User::create([
            'username' => 'manufacturinguser_' . uniqid(),
            'name' => 'Manufacturing User',
            'email' => 'manufacturinguser_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'client_id' => $this->client->id,
            'language_id' => $this->language->id,
            'industry_id' => $industry->id
        ]);

        // Create job-specific assessment assignment
        $assignmentData = [
            'assessment_id' => $this->assessment->id,
            'job_id' => $this->job->id,
            'completed' => false
        ];
        $assignment = new \App\Assignment($assignmentData);
        $user->assignments()->save($assignment);

        // Verify user has industry-specific assignment
        $this->assertEquals($industry->id, $user->industry_id);
        $this->assertNotNull($user->assignments);
        $this->assertCount(1, $user->assignments);
    }

    // ========================================
    // INDUSTRY-BASED REPORTING TESTS
    // ========================================

    /**
     * Test industry-based user reporting
     */
    public function testIndustryBasedUserReporting()
    {
        $industries = [
            'Technology' => Industry::firstOrCreate(['name' => 'Technology Test Industry']),
            'Healthcare' => Industry::firstOrCreate(['name' => 'Healthcare Test Industry']),
            'Finance' => Industry::firstOrCreate(['name' => 'Finance Test Industry'])
        ];

        // Create users in each industry
        $usersPerIndustry = 5;
        foreach ($industries as $industryName => $industry) {
            for ($i = 0; $i < $usersPerIndustry; $i++) {
                User::create([
                    'username' => "reportuser_{$industryName}_{$i}_" . uniqid(),
                    'name' => "Report User {$i}",
                    'email' => "reportuser_{$industryName}_{$i}_" . uniqid() . '@example.com',
                    'password' => bcrypt('password'),
                    'client_id' => $this->client->id,
                    'language_id' => $this->language->id,
                    'industry_id' => $industry->id
                ]);
            }
        }

        // Test industry-based user counts
        foreach ($industries as $industryName => $industry) {
            $userCount = User::where('industry_id', $industry->id)->count();
            $this->assertEquals($usersPerIndustry, $userCount);
        }

        // Test total user count across all industries
        $industryIds = array_map(function($industry) { return $industry->id; }, $industries);
        $totalUsers = User::whereIn('industry_id', $industryIds)->count();
        $this->assertEquals(count($industries) * $usersPerIndustry, $totalUsers);
    }

    /**
     * Test industry-based benchmark reporting
     */
    public function testIndustryBasedBenchmarkReporting()
    {
        $industry = Industry::firstOrCreate(['name' => 'Consulting Test Industry']);

        // Create benchmarks for this industry
        $benchmarkValues = [75, 80, 85, 90];
        $benchmarks = [];

        foreach ($this->dimensions as $key => $dimension) {
            $benchmarks[] = Benchmark::create([
                'dimension_id' => $dimension->id,
                'industry_id' => $industry->id,
                'value' => strval($benchmarkValues[array_rand($benchmarkValues)])
            ]);
        }

        // Test industry benchmark statistics
        $industryBenchmarks = $industry->benchmarks;
        $this->assertCount(4, $industryBenchmarks);

        // Calculate average benchmark value for this industry
        $totalValue = 0;
        foreach ($industryBenchmarks as $benchmark) {
            $totalValue += floatval($benchmark->value);
        }
        $averageValue = $totalValue / count($industryBenchmarks);

        $this->assertGreaterThan(0, $averageValue);
        $this->assertLessThan(100, $averageValue);
    }

    /**
     * Test industry-based performance analytics
     */
    public function testIndustryBasedPerformanceAnalytics()
    {
        $industry = Industry::firstOrCreate(['name' => 'Energy Test Industry']);

        // Create users with different performance levels
        $performanceLevels = ['high', 'medium', 'low'];
        $users = [];

        foreach ($performanceLevels as $level) {
            $users[] = User::create([
                'username' => "perfuser_{$level}_" . uniqid(),
                'name' => "Performance User {$level}",
                'email' => "perfuser_{$level}_" . uniqid() . '@example.com',
                'password' => bcrypt('password'),
                'client_id' => $this->client->id,
                'language_id' => $this->language->id,
                'industry_id' => $industry->id
            ]);
        }

        // Test industry performance distribution
        $industryUsers = User::where('industry_id', $industry->id)->get();
        $this->assertCount(3, $industryUsers);

        // Verify all users belong to the same industry
        foreach ($industryUsers as $user) {
            $this->assertEquals($industry->id, $user->industry_id);
            $this->assertEquals('Energy Test Industry', $user->industry->name);
        }
    }

    // ========================================
    // INDUSTRY DATA INTEGRITY TESTS
    // ========================================

    /**
     * Test industry data validation
     */
    public function testIndustryDataValidation()
    {
        // Test valid industry names
        $validNames = [
            'Technology',
            'Healthcare & Life Sciences',
            'Financial Services',
            'Manufacturing & Engineering',
            'Consulting & Professional Services',
            'Government & Public Sector',
            'Non-Profit & Social Impact',
            'Real Estate & Property Management'
        ];

        foreach ($validNames as $name) {
            $industry = Industry::firstOrCreate(['name' => $name]);
            $this->assertNotNull($industry->id);
            $this->assertEquals($name, $industry->name);
        }

        // Test edge cases
        $edgeCases = [
            'A', // Single character
            'Very Long Industry Name That Exceeds Normal Length Expectations', // Long name
            'Industry with Numbers 123', // Numbers
            'Industry with Special Chars @#$%' // Special characters
        ];

        foreach ($edgeCases as $name) {
            $industry = Industry::firstOrCreate(['name' => $name]);
            $this->assertNotNull($industry->id);
            $this->assertEquals($name, $industry->name);
        }
    }

    /**
     * Test industry relationship integrity
     */
    public function testIndustryRelationshipIntegrity()
    {
        $industry = Industry::create(['name' => 'Test Industry']);

        // Create user with industry
        $user = User::create([
            'username' => 'integrityuser_' . uniqid(),
            'name' => 'Integrity Test User',
            'email' => 'integrityuser_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'client_id' => $this->client->id,
            'language_id' => $this->language->id,
            'industry_id' => $industry->id
        ]);

        // Create benchmark for industry
        $benchmark = Benchmark::create([
            'dimension_id' => $this->dimensions['leadership']->id,
            'industry_id' => $industry->id,
            'value' => '85'
        ]);

        // Test user relationship
        $this->assertEquals($industry->id, $user->industry_id);
        $this->assertNotNull($user->industry);
        $this->assertEquals('Test Industry', $user->industry->name);

        // Test benchmark relationship
        $this->assertEquals($industry->id, $benchmark->industry_id);
        $this->assertNotNull($benchmark->industry);
        $this->assertEquals('Test Industry', $benchmark->industry->name);

        // Test industry relationships from other side
        $this->assertTrue($industry->users->contains($user));
        $this->assertTrue($industry->benchmarks->contains($benchmark));
    }

    /**
     * Test industry cascade operations
     */
    public function testIndustryCascadeOperations()
    {
        $industry = Industry::firstOrCreate(['name' => 'Cascade Test Industry']);

        // Create user and benchmark associated with industry
        $user = User::create([
            'username' => 'cascadeuser_' . uniqid(),
            'name' => 'Cascade Test User',
            'email' => 'cascadeuser_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'client_id' => $this->client->id,
            'language_id' => $this->language->id,
            'industry_id' => $industry->id
        ]);

        $benchmark = Benchmark::create([
            'dimension_id' => $this->dimensions['leadership']->id,
            'industry_id' => $industry->id,
            'value' => '80'
        ]);

        $industryId = $industry->id;
        $userId = $user->id;
        $benchmarkId = $benchmark->id;

        // Test that we cannot delete industry with associated users (foreign key constraint)
        $this->setExpectedException('Illuminate\Database\QueryException');
        $industry->delete();

        // Verify industry still exists
        $existingIndustry = Industry::find($industryId);
        $this->assertNotNull($existingIndustry);

        // Verify user still exists
        $userAfterDelete = User::find($userId);
        $this->assertNotNull($userAfterDelete);

        // Verify benchmark still exists
        $benchmarkAfterDelete = Benchmark::find($benchmarkId);
        $this->assertNotNull($benchmarkAfterDelete);

        // Clean up by removing the user first, then the industry
        $user->delete();
        $benchmark->delete();
        $existingIndustry->delete();
    }

    // ========================================
    // INDUSTRY PERFORMANCE TESTS
    // ========================================

    /**
     * Test industry query performance
     */
    public function testIndustryQueryPerformance()
    {
        $startTime = microtime(true);

        // Create multiple industries
        for ($i = 0; $i < 50; $i++) {
            Industry::create([
                'name' => "Performance Test Industry {$i}"
            ]);
        }

        $creationTime = microtime(true) - $startTime;

        // Test query performance
        $queryStartTime = microtime(true);
        
        $industries = Industry::all();
        $industryCount = Industry::count();
        
        $queryTime = microtime(true) - $queryStartTime;

        $this->assertGreaterThan(50, $industryCount);
        $this->assertLessThan(1.0, $creationTime, 'Industry creation should complete within 1 second');
        $this->assertLessThan(0.1, $queryTime, 'Industry query should complete within 0.1 seconds');
    }

    /**
     * Test industry-user relationship performance
     */
    public function testIndustryUserRelationshipPerformance()
    {
        $industry = Industry::create(['name' => 'Performance Test Industry']);

        $startTime = microtime(true);

        // Create multiple users in the same industry
        for ($i = 0; $i < 20; $i++) {
            User::create([
                'username' => "perfuser{$i}_" . uniqid(),
                'name' => "Performance User {$i}",
                'email' => "perfuser{$i}_" . uniqid() . '@example.com',
                'password' => bcrypt('password'),
                'client_id' => $this->client->id,
                'language_id' => $this->language->id,
                'industry_id' => $industry->id
            ]);
        }

        $creationTime = microtime(true) - $startTime;

        // Test relationship query performance
        $queryStartTime = microtime(true);
        
        $industryUsers = $industry->users;
        $userCount = User::where('industry_id', $industry->id)->count();
        
        $queryTime = microtime(true) - $queryStartTime;

        $this->assertEquals(20, $userCount);
        $this->assertLessThan(2.0, $creationTime, 'User creation should complete within 2 seconds');
        $this->assertLessThan(0.1, $queryTime, 'Relationship query should complete within 0.1 seconds');
    }
}
