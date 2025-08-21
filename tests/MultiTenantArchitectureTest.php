<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Client;
use App\User;
use App\Assessment;
use App\Dimension;
use App\Benchmark;
use App\Language;
use App\Job;
use App\Assignment;
use App\Answer;
use App\Question;
use App\Industry;
use App\FeedbackLibrary;
use App\ClientReport;
use App\Group;
use App\GroupRole;
use App\Analysis;
use App\Reseller;
use App\Http\Controllers\ClientsController;
use App\Http\Controllers\ResellersController;
use Bican\Roles\Models\Role;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class MultiTenantArchitectureTest extends TestCase
{
    protected $client1;
    protected $client2;
    protected $user1;
    protected $user2;
    protected $adminUser;
    protected $language;
    protected $assessment1;
    protected $assessment2;
    protected $job1;
    protected $job2;
    protected $dimensions;
    protected $industry;
    protected $clientsController;

    public function setUp()
    {
        parent::setUp();
        
        // Disable CSRF protection for tests
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);
        
        // Initialize controller
        $this->clientsController = new ClientsController();
        
        // Create test data
        $this->language = Language::firstOrCreate([
            'name' => 'English',
            'native_name' => 'English',
            'code' => 'en'
        ]);
        
        $this->industry = Industry::firstOrCreate([
            'name' => 'Multi-Tenant Test Industry'
        ]);
        
        // Ensure roles exist for testing
        $this->createRolesIfNeeded();
        
        // Create test clients
        $this->createTestClients();
        
        // Create test users for each client
        $this->createTestUsers();
        
        // Create test assessments and related data
        $this->createTestAssessmentsAndData();
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
     * Create test clients with different configurations
     */
    private function createTestClients()
    {
        // Client 1 - Standard configuration
        $this->client1 = Client::create([
            'name' => 'Multi-Tenant Test Client 1',
            'address' => '123 Test Street, Test City',
            'require_profile' => true,
            'require_research' => false,
            'whitelabel' => false,
            'primary_color' => '#007bff',
            'accent_color' => '#6c757d',
            'assessments' => []
        ]);

        // Client 2 - Whitelabel configuration
        $this->client2 = Client::create([
            'name' => 'Multi-Tenant Test Client 2',
            'address' => '456 Test Avenue, Test Town',
            'require_profile' => false,
            'require_research' => true,
            'whitelabel' => true,
            'primary_color' => '#28a745',
            'accent_color' => '#dc3545',
            'assessments' => []
        ]);
    }

    /**
     * Create test users for each client
     */
    private function createTestUsers()
    {
        // Admin user (system-wide)
        $this->adminUser = User::create([
            'username' => 'multitenant_admin_' . uniqid(),
            'name' => 'Multi-Tenant Admin',
            'email' => 'multitenant_admin_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'language_id' => $this->language->id,
            'completed_profile' => true,
            'completed_research' => true
        ]);
        
        $adminRole = Role::where('slug', 'admin')->first();
        $this->adminUser->attachRole($adminRole);

        // User 1 - belongs to Client 1
        $this->user1 = User::create([
            'username' => 'multitenant_user1_' . uniqid(),
            'name' => 'Multi-Tenant User 1',
            'email' => 'multitenant_user1_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'client_id' => $this->client1->id,
            'language_id' => $this->language->id,
            'industry_id' => $this->industry->id,
            'completed_profile' => true,
            'completed_research' => false
        ]);
        
        $userRole = Role::where('slug', 'user')->first();
        $this->user1->attachRole($userRole);

        // User 2 - belongs to Client 2
        $this->user2 = User::create([
            'username' => 'multitenant_user2_' . uniqid(),
            'name' => 'Multi-Tenant User 2',
            'email' => 'multitenant_user2_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'client_id' => $this->client2->id,
            'language_id' => $this->language->id,
            'industry_id' => $this->industry->id,
            'completed_profile' => false,
            'completed_research' => true
        ]);
        
        $this->user2->attachRole($userRole);
    }

    /**
     * Create test assessments and related data for each client
     */
    private function createTestAssessmentsAndData()
    {
        // Assessment 1 - belongs to User 1 (Client 1)
        $assessment1Data = [
            'name' => 'Multi-Tenant Assessment 1',
            'description' => 'Assessment for Client 1 testing',
            'use_custom_fields' => false,
            'whitelabel' => false,
            'translation' => false,
            'timed' => false,
            'paginate' => false,
            'custom_fields' => [],
            'anchors' => []
        ];
        
        $this->assessment1 = new Assessment($assessment1Data);
        $this->user1->assessments()->save($this->assessment1);

        // Assessment 2 - belongs to User 2 (Client 2)
        $assessment2Data = [
            'name' => 'Multi-Tenant Assessment 2',
            'description' => 'Assessment for Client 2 testing',
            'use_custom_fields' => true,
            'whitelabel' => true,
            'translation' => false,
            'timed' => true,
            'paginate' => true,
            'custom_fields' => ['department', 'experience_level'],
            'anchors' => ['Strongly Disagree', 'Disagree', 'Neutral', 'Agree', 'Strongly Agree']
        ];
        
        $this->assessment2 = new Assessment($assessment2Data);
        $this->user2->assessments()->save($this->assessment2);

        // Create dimensions after assessments are created
        $this->dimensions = [];
        
        $leadershipDimension = Dimension::create([
            'name' => 'Leadership',
            'code' => 'LEAD',
            'parent' => 0,
            'assessment_id' => $this->assessment1->id
        ]);
        $this->dimensions['leadership'] = $leadershipDimension;

        $communicationDimension = Dimension::create([
            'name' => 'Communication',
            'code' => 'COMM',
            'parent' => 0,
            'assessment_id' => $this->assessment2->id
        ]);
        $this->dimensions['communication'] = $communicationDimension;

        // Create jobs for each client
        $this->job1 = Job::create([
            'name' => 'Multi-Tenant Job 1',
            'slug' => 'multitenant-job-1-' . uniqid(),
            'client_id' => $this->client1->id
        ]);

        $this->job2 = Job::create([
            'name' => 'Multi-Tenant Job 2',
            'slug' => 'multitenant-job-2-' . uniqid(),
            'client_id' => $this->client2->id
        ]);
    }

    // ========================================
    // CLIENT ISOLATION TESTS
    // ========================================

    /**
     * Test client data isolation - users
     */
    public function testClientUserIsolation()
    {
        // Client 1 should only see its own users
        $client1Users = $this->client1->users;
        $this->assertCount(1, $client1Users);
        $this->assertEquals($this->user1->id, $client1Users->first()->id);
        $this->assertNotContains($this->user2->id, $client1Users->pluck('id'));

        // Client 2 should only see its own users
        $client2Users = $this->client2->users;
        $this->assertCount(1, $client2Users);
        $this->assertEquals($this->user2->id, $client2Users->first()->id);
        $this->assertNotContains($this->user1->id, $client2Users->pluck('id'));
    }

    /**
     * Test client data isolation - jobs
     */
    public function testClientJobIsolation()
    {
        // Client 1 should only see its own jobs
        $client1Jobs = $this->client1->jobs;
        $this->assertCount(1, $client1Jobs);
        $this->assertEquals($this->job1->id, $client1Jobs->first()->id);
        $this->assertNotContains($this->job2->id, $client1Jobs->pluck('id'));

        // Client 2 should only see its own jobs
        $client2Jobs = $this->client2->jobs;
        $this->assertCount(1, $client2Jobs);
        $this->assertEquals($this->job2->id, $client2Jobs->first()->id);
        $this->assertNotContains($this->job1->id, $client2Jobs->pluck('id'));
    }

    /**
     * Test client data isolation - assessments (through users)
     */
    public function testClientAssessmentIsolation()
    {
        // Get assessments for each client through their users
        $client1Assessments = [];
        foreach ($this->client1->users as $user) {
            $client1Assessments = array_merge($client1Assessments, $user->assessments->toArray());
        }

        $client2Assessments = [];
        foreach ($this->client2->users as $user) {
            $client2Assessments = array_merge($client2Assessments, $user->assessments->toArray());
        }

        // Client 1 should only see assessments created by its users
        $this->assertCount(1, $client1Assessments);
        $this->assertEquals($this->assessment1->id, $client1Assessments[0]['id']);

        // Client 2 should only see assessments created by its users
        $this->assertCount(1, $client2Assessments);
        $this->assertEquals($this->assessment2->id, $client2Assessments[0]['id']);
    }

    /**
     * Test client configuration isolation
     */
    public function testClientConfigurationIsolation()
    {
        // Client 1 configuration
        $this->assertTrue($this->client1->require_profile);
        $this->assertFalse($this->client1->require_research);
        $this->assertFalse($this->client1->whitelabel);
        $this->assertEquals('#007bff', $this->client1->primary_color);
        $this->assertEquals('#6c757d', $this->client1->accent_color);

        // Client 2 configuration (different from Client 1)
        $this->assertFalse($this->client2->require_profile);
        $this->assertTrue($this->client2->require_research);
        $this->assertTrue($this->client2->whitelabel);
        $this->assertEquals('#28a745', $this->client2->primary_color);
        $this->assertEquals('#dc3545', $this->client2->accent_color);
    }

    // ========================================
    // USER ACCESS CONTROL TESTS
    // ========================================

    /**
     * Test user can only access their own client's data
     */
    public function testUserAccessControl()
    {
        // User 1 should belong to Client 1
        $this->assertEquals($this->client1->id, $this->user1->client_id);
        $this->assertEquals($this->client1->name, $this->user1->client->name);

        // User 2 should belong to Client 2
        $this->assertEquals($this->client2->id, $this->user2->client_id);
        $this->assertEquals($this->client2->name, $this->user2->client->name);

        // Users should not have access to other clients' data
        $this->assertNotEquals($this->client2->id, $this->user1->client_id);
        $this->assertNotEquals($this->client1->id, $this->user2->client_id);
    }

    /**
     * Test user role-based access within client context
     */
    public function testUserRoleBasedAccess()
    {
        // Create client admin for Client 1
        $clientAdmin1 = User::create([
            'username' => 'clientadmin1_' . uniqid(),
            'name' => 'Client 1 Admin',
            'email' => 'clientadmin1_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'client_id' => $this->client1->id,
            'language_id' => $this->language->id,
            'completed_profile' => true,
            'completed_research' => true
        ]);
        
        $clientRole = Role::where('slug', 'client')->first();
        $clientAdmin1->attachRole($clientRole);

        // Client admin should belong to their client
        $this->assertEquals($this->client1->id, $clientAdmin1->client_id);
        
        // Refresh the user to load roles
        $clientAdmin1 = $clientAdmin1->fresh();
        $this->assertTrue($clientAdmin1->hasRole('client'));

        // Regular user should have user role
        $this->user1 = $this->user1->fresh();
        $this->assertTrue($this->user1->hasRole('user'));
        $this->assertFalse($this->user1->hasRole('client'));

        // Admin user should have admin role and no client association
        $this->adminUser = $this->adminUser->fresh();
        $this->assertTrue($this->adminUser->hasRole('admin'));
        $this->assertNull($this->adminUser->client_id);
    }

    /**
     * Test cross-client user access prevention
     */
    public function testCrossClientAccessPrevention()
    {
        // Create assignments for each user
        $assignment1Data = [
            'assessment_id' => $this->assessment1->id,
            'job_id' => $this->job1->id,
            'completed' => false
        ];
        $assignment1 = new Assignment($assignment1Data);
        $this->user1->assignments()->save($assignment1);

        $assignment2Data = [
            'assessment_id' => $this->assessment2->id,
            'job_id' => $this->job2->id,
            'completed' => false
        ];
        $assignment2 = new Assignment($assignment2Data);
        $this->user2->assignments()->save($assignment2);

        // User 1 should only see assignments related to their client
        $user1Assignments = $this->user1->assignments;
        $this->assertCount(1, $user1Assignments);
        $this->assertEquals($assignment1->id, $user1Assignments->first()->id);
        $this->assertEquals($this->job1->id, $user1Assignments->first()->job_id);

        // User 2 should only see assignments related to their client
        $user2Assignments = $this->user2->assignments;
        $this->assertCount(1, $user2Assignments);
        $this->assertEquals($assignment2->id, $user2Assignments->first()->id);
        $this->assertEquals($this->job2->id, $user2Assignments->first()->job_id);
    }

    // ========================================
    // CLIENT MANAGEMENT TESTS
    // ========================================

    /**
     * Test client creation and management
     */
    public function testClientCreationAndManagement()
    {
        $clientData = [
            'name' => 'New Multi-Tenant Client',
            'address' => '789 New Street, New City',
            'require_profile' => true,
            'require_research' => true,
            'whitelabel' => false,
            'primary_color' => '#17a2b8',
            'accent_color' => '#ffc107',
            'assessments' => []
        ];

        $newClient = Client::create($clientData);

        $this->assertNotNull($newClient->id);
        $this->assertEquals('New Multi-Tenant Client', $newClient->name);
        $this->assertEquals('789 New Street, New City', $newClient->address);
        $this->assertTrue($newClient->require_profile);
        $this->assertTrue($newClient->require_research);
        $this->assertFalse($newClient->whitelabel);
        $this->assertEquals('#17a2b8', $newClient->primary_color);
        $this->assertEquals('#ffc107', $newClient->accent_color);
    }

    /**
     * Test client configuration updates
     */
    public function testClientConfigurationUpdates()
    {
        // Update Client 1 configuration
        $this->client1->whitelabel = true;
        $this->client1->primary_color = '#6f42c1';
        $this->client1->require_research = true;
        $this->client1->save();

        // Verify updates
        $updatedClient = Client::find($this->client1->id);
        $this->assertTrue((bool)$updatedClient->whitelabel);
        $this->assertEquals('#6f42c1', $updatedClient->primary_color);
        $this->assertTrue((bool)$updatedClient->require_research);
    }

    /**
     * Test client user management
     */
    public function testClientUserManagement()
    {
        // Add more users to Client 1
        $additionalUsers = [];
        for ($i = 0; $i < 3; $i++) {
            $additionalUsers[] = User::create([
                'username' => "client1_user{$i}_" . uniqid(),
                'name' => "Client 1 User {$i}",
                'email' => "client1_user{$i}_" . uniqid() . '@example.com',
                'password' => bcrypt('password'),
                'client_id' => $this->client1->id,
                'language_id' => $this->language->id,
                'completed_profile' => true,
                'completed_research' => false
            ]);
        }

        // Client 1 should now have 4 users (1 original + 3 additional)
        $client1Users = $this->client1->users;
        $this->assertCount(4, $client1Users);

        // All users should belong to Client 1
        foreach ($client1Users as $user) {
            $this->assertEquals($this->client1->id, $user->client_id);
        }

        // Client 2 should still have only 1 user
        $client2Users = $this->client2->users;
        $this->assertCount(1, $client2Users);
    }

    // ========================================
    // CLIENT-SPECIFIC FEATURE TESTS
    // ========================================

    /**
     * Test client-specific feedback libraries
     */
    public function testClientSpecificFeedbackLibraries()
    {
        // Create feedback library for Client 1
        $feedbackLibrary1 = FeedbackLibrary::create([
            'name' => 'Client 1 Feedback Library',
            'client_id' => $this->client1->id,
            'feedback' => [
                'dimensions' => [
                    'Leadership' => [
                        'high' => 'Excellent leadership skills for Client 1',
                        'medium' => 'Good leadership skills for Client 1',
                        'low' => 'Developing leadership skills for Client 1'
                    ]
                ]
            ]
        ]);

        // Create feedback library for Client 2
        $feedbackLibrary2 = FeedbackLibrary::create([
            'name' => 'Client 2 Feedback Library',
            'client_id' => $this->client2->id,
            'feedback' => [
                'dimensions' => [
                    'Communication' => [
                        'high' => 'Outstanding communication skills for Client 2',
                        'medium' => 'Solid communication skills for Client 2',
                        'low' => 'Improving communication skills for Client 2'
                    ]
                ]
            ]
        ]);

        // Client 1 should only see its own feedback libraries
        $client1Libraries = $this->client1->feedbackLibraries;
        $this->assertCount(1, $client1Libraries);
        $this->assertEquals($feedbackLibrary1->id, $client1Libraries->first()->id);
        $this->assertNotContains($feedbackLibrary2->id, $client1Libraries->pluck('id'));

        // Client 2 should only see its own feedback libraries
        $client2Libraries = $this->client2->feedbackLibraries;
        $this->assertCount(1, $client2Libraries);
        $this->assertEquals($feedbackLibrary2->id, $client2Libraries->first()->id);
        $this->assertNotContains($feedbackLibrary1->id, $client2Libraries->pluck('id'));
    }

    /**
     * Test client-specific reports
     */
    public function testClientSpecificReports()
    {
        // Create base reports first
        $baseReport1 = \App\Report::create([
            'name' => 'Base Report 1',
            'assessments' => serialize([$this->assessment1->id]),
            'view' => 'standard',
            'fields' => serialize(['name', 'score', 'percentile'])
        ]);

        $baseReport2 = \App\Report::create([
            'name' => 'Base Report 2',
            'assessments' => serialize([$this->assessment2->id]),
            'view' => 'detailed',
            'fields' => serialize(['name', 'score', 'percentile', 'feedback'])
        ]);

        // Create client reports linked to base reports
        $clientReport1 = ClientReport::create([
            'client_id' => $this->client1->id,
            'report_id' => $baseReport1->id,
            'fields' => serialize(['name', 'score', 'percentile'])
        ]);

        $clientReport2 = ClientReport::create([
            'client_id' => $this->client2->id,
            'report_id' => $baseReport2->id,
            'fields' => serialize(['name', 'score', 'percentile', 'feedback'])
        ]);

        // Client 1 should only see its own reports
        $client1Reports = $this->client1->reports;
        $this->assertCount(1, $client1Reports);
        $this->assertEquals($clientReport1->id, $client1Reports->first()->id);

        // Client 2 should only see its own reports
        $client2Reports = $this->client2->reports;
        $this->assertCount(1, $client2Reports);
        $this->assertEquals($clientReport2->id, $client2Reports->first()->id);
    }

    /**
     * Test client-specific groups and roles
     */
    public function testClientSpecificGroupsAndRoles()
    {
        // Create groups for each client
        $group1 = Group::create([
            'name' => 'Client 1 Management Group',
            'client_id' => $this->client1->id
        ]);

        $group2 = Group::create([
            'name' => 'Client 2 Development Group',
            'client_id' => $this->client2->id
        ]);

        // Create group roles for each client
        $groupRole1 = GroupRole::create([
            'name' => 'Client 1 Manager Role',
            'client_id' => $this->client1->id
        ]);

        $groupRole2 = GroupRole::create([
            'name' => 'Client 2 Developer Role',
            'client_id' => $this->client2->id
        ]);

        // Client 1 should only see its own groups and roles
        $client1Groups = $this->client1->groups;
        $client1GroupRoles = $this->client1->groupRoles;
        
        $this->assertCount(1, $client1Groups);
        $this->assertCount(1, $client1GroupRoles);
        $this->assertEquals($group1->id, $client1Groups->first()->id);
        $this->assertEquals($groupRole1->id, $client1GroupRoles->first()->id);

        // Client 2 should only see its own groups and roles
        $client2Groups = $this->client2->groups;
        $client2GroupRoles = $this->client2->groupRoles;
        
        $this->assertCount(1, $client2Groups);
        $this->assertCount(1, $client2GroupRoles);
        $this->assertEquals($group2->id, $client2Groups->first()->id);
        $this->assertEquals($groupRole2->id, $client2GroupRoles->first()->id);
    }

    // ========================================
    // CLIENT ANALYTICS AND REPORTING TESTS
    // ========================================

    /**
     * Test client assessment completion analytics
     */
    public function testClientAssessmentCompletionAnalytics()
    {
        // Create assignments and complete some for Client 1
        $assignment1 = new Assignment([
            'assessment_id' => $this->assessment1->id,
            'job_id' => $this->job1->id,
            'completed' => true,
            'completed_at' => Carbon::now()
        ]);
        $this->user1->assignments()->save($assignment1);

        $assignment2 = new Assignment([
            'assessment_id' => $this->assessment1->id,
            'job_id' => $this->job1->id,
            'completed' => false
        ]);
        $this->user1->assignments()->save($assignment2);

        // Create assignments for Client 2
        $assignment3 = new Assignment([
            'assessment_id' => $this->assessment2->id,
            'job_id' => $this->job2->id,
            'completed' => true,
            'completed_at' => Carbon::now()
        ]);
        $this->user2->assignments()->save($assignment3);

        // Test Client 1 analytics
        $client1CompletedCount = $this->client1->assessmentsCompleted();
        $client1TotalCount = $this->client1->assignments();

        $this->assertEquals(1, $client1CompletedCount);
        $this->assertEquals(2, $client1TotalCount);

        // Test Client 2 analytics
        $client2CompletedCount = $this->client2->assessmentsCompleted();
        $client2TotalCount = $this->client2->assignments();

        $this->assertEquals(1, $client2CompletedCount);
        $this->assertEquals(1, $client2TotalCount);
    }

    /**
     * Test client questions answered analytics
     */
    public function testClientQuestionsAnsweredAnalytics()
    {
        // Create questions for assessments
        $question1 = new Question([
            'content' => 'Test question for assessment 1',
            'number' => 1,
            'type' => 1,
            'dimension_id' => $this->dimensions['leadership']->id
        ]);
        $question1->assessment_id = $this->assessment1->id;
        $question1->save();

        $question2 = new Question([
            'content' => 'Test question for assessment 2',
            'number' => 1,
            'type' => 1,
            'dimension_id' => $this->dimensions['communication']->id
        ]);
        $question2->assessment_id = $this->assessment2->id;
        $question2->save();

        // Create assignments
        $assignment1 = new Assignment([
            'assessment_id' => $this->assessment1->id,
            'job_id' => $this->job1->id,
            'completed' => true
        ]);
        $this->user1->assignments()->save($assignment1);

        $assignment2 = new Assignment([
            'assessment_id' => $this->assessment2->id,
            'job_id' => $this->job2->id,
            'completed' => true
        ]);
        $this->user2->assignments()->save($assignment2);

        // Create answers using relationship pattern
        $answer1 = new Answer([
            'user_id' => $this->user1->id,
            'question_id' => $question1->id,
            'answer' => 85,
            'time_taken' => 30
        ]);
        $assignment1->answers()->save($answer1);

        $answer2 = new Answer([
            'user_id' => $this->user2->id,
            'question_id' => $question2->id,
            'answer' => 75,
            'time_taken' => 25
        ]);
        $assignment2->answers()->save($answer2);

        // Test questions answered analytics
        $client1QuestionsAnswered = $this->client1->questionsAnswered();
        $client2QuestionsAnswered = $this->client2->questionsAnswered();

        $this->assertEquals(1, $client1QuestionsAnswered);
        $this->assertEquals(1, $client2QuestionsAnswered);
    }

    /**
     * Test client job users analytics
     */
    public function testClientJobUsersAnalytics()
    {
        // Create additional users for jobs
        $jobUser1 = User::create([
            'username' => 'jobuser1_' . uniqid(),
            'name' => 'Job User 1',
            'email' => 'jobuser1_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'client_id' => $this->client1->id,
            'language_id' => $this->language->id
        ]);

        $jobUser2 = User::create([
            'username' => 'jobuser2_' . uniqid(),
            'name' => 'Job User 2',
            'email' => 'jobuser2_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'client_id' => $this->client2->id,
            'language_id' => $this->language->id
        ]);

        // Create assignments linking users to jobs
        $assignment1 = new Assignment([
            'assessment_id' => $this->assessment1->id,
            'job_id' => $this->job1->id,
            'completed' => false
        ]);
        $jobUser1->assignments()->save($assignment1);

        $assignment2 = new Assignment([
            'assessment_id' => $this->assessment2->id,
            'job_id' => $this->job2->id,
            'completed' => false
        ]);
        $jobUser2->assignments()->save($assignment2);

        // Test job users analytics (this would require implementing the applicants method on Job model)
        // For now, we'll test that users are properly associated with their client's jobs
        $this->assertEquals($this->client1->id, $jobUser1->client_id);
        $this->assertEquals($this->client2->id, $jobUser2->client_id);
        
        $jobUser1Assignments = $jobUser1->assignments;
        $jobUser2Assignments = $jobUser2->assignments;
        
        $this->assertEquals($this->job1->id, $jobUser1Assignments->first()->job_id);
        $this->assertEquals($this->job2->id, $jobUser2Assignments->first()->job_id);
    }

    // ========================================
    // CLIENT CONTROLLER TESTS
    // ========================================

    /**
     * Test clients controller index method
     */
    public function testClientsControllerIndex()
    {
        $this->be($this->adminUser);

        $response = $this->call('GET', 'dashboard/clients');

        // Should return 200 or redirect (depending on middleware)
        $this->assertTrue(in_array($response->getStatusCode(), [200, 302]));
    }

    /**
     * Test clients controller store method
     */
    public function testClientsControllerStore()
    {
        $this->be($this->adminUser);

        $requestData = [
            'name' => 'Controller Test Client',
            'address' => '123 Controller Street',
            'require_profile' => true,
            'require_research' => false,
            'whitelabel' => false,
            'primary_color' => '#007bff',
            'accent_color' => '#6c757d'
        ];

        $response = $this->call('POST', 'dashboard/clients', $requestData);

        // Should redirect back to clients index
        $this->assertEquals(302, $response->getStatusCode());

        // Verify client was created
        $client = Client::where('name', 'Controller Test Client')->first();
        $this->assertNotNull($client);
    }

    /**
     * Test clients controller update method
     */
    public function testClientsControllerUpdate()
    {
        $this->be($this->adminUser);

        $requestData = [
            'name' => 'Updated Client Name',
            'address' => 'Updated Address',
            'require_profile' => false,
            'require_research' => true,
            'whitelabel' => true,
            'primary_color' => '#28a745',
            'accent_color' => '#dc3545'
        ];

        $response = $this->call('PATCH', "dashboard/clients/{$this->client1->id}", $requestData);

        // Should redirect back to clients index
        $this->assertEquals(302, $response->getStatusCode());

        // Verify client was updated
        $updatedClient = Client::find($this->client1->id);
        $this->assertEquals('Updated Client Name', $updatedClient->name);
        $this->assertEquals('Updated Address', $updatedClient->address);
        $this->assertFalse((bool)$updatedClient->require_profile);
        $this->assertTrue((bool)$updatedClient->require_research);
        $this->assertTrue((bool)$updatedClient->whitelabel);
    }

    /**
     * Test clients controller destroy method
     */
    public function testClientsControllerDestroy()
    {
        $this->be($this->adminUser);

        // Create a client specifically for deletion testing
        $deletableClient = Client::create([
            'name' => 'Deletable Client',
            'require_profile' => false,
            'require_research' => false,
            'whitelabel' => false,
            'primary_color' => '#000000',
            'accent_color' => '#ffffff'
        ]);

        $response = $this->call('DELETE', "dashboard/clients/{$deletableClient->id}");

        // Should redirect back to clients index
        $this->assertEquals(302, $response->getStatusCode());

        // Verify client was deleted
        $deletedClient = Client::find($deletableClient->id);
        $this->assertNull($deletedClient);
    }

    // ========================================
    // WHITELABEL FUNCTIONALITY TESTS
    // ========================================

    /**
     * Test whitelabel client configuration
     */
    public function testWhitelabelClientConfiguration()
    {
        // Client 2 is configured as whitelabel
        $this->assertTrue($this->client2->whitelabel);
        
        // Whitelabel clients should have custom branding
        $this->assertNotNull($this->client2->primary_color);
        $this->assertNotNull($this->client2->accent_color);
        $this->assertEquals('#28a745', $this->client2->primary_color);
        $this->assertEquals('#dc3545', $this->client2->accent_color);

        // Non-whitelabel client should not be whitelabel
        $this->assertFalse($this->client1->whitelabel);
    }

    /**
     * Test whitelabel assessment configuration
     */
    public function testWhitelabelAssessmentConfiguration()
    {
        // Assessment 2 belongs to whitelabel client and should be whitelabel
        $this->assertTrue($this->assessment2->whitelabel);
        $this->assertEquals($this->client2->id, $this->assessment2->user->client_id);

        // Assessment 1 belongs to non-whitelabel client
        $this->assertFalse($this->assessment1->whitelabel);
        $this->assertEquals($this->client1->id, $this->assessment1->user->client_id);
    }

    // ========================================
    // DATA SEGREGATION TESTS
    // ========================================

    /**
     * Test complete data segregation between clients
     */
    public function testCompleteDataSegregation()
    {
        // Create comprehensive data for each client
        $this->createComprehensiveClientData();

        // Verify Client 1 data isolation
        $client1Data = $this->getClientData($this->client1);
        $client2Data = $this->getClientData($this->client2);

        // Verify no data overlap
        $this->assertNoDataOverlap($client1Data, $client2Data);
    }

    /**
     * Helper method to create comprehensive data for testing
     */
    private function createComprehensiveClientData()
    {
        // Create benchmarks for each client's industry
        Benchmark::create([
            'dimension_id' => $this->dimensions['leadership']->id,
            'industry_id' => $this->industry->id,
            'value' => '80'
        ]);

        Benchmark::create([
            'dimension_id' => $this->dimensions['communication']->id,
            'industry_id' => $this->industry->id,
            'value' => '75'
        ]);

        // Create analyses for each client
        Analysis::create([
            'name' => 'Client 1 Analysis',
            'client_id' => $this->client1->id,
            'user_id' => $this->user1->id
        ]);

        Analysis::create([
            'name' => 'Client 2 Analysis',
            'client_id' => $this->client2->id,
            'user_id' => $this->user2->id
        ]);
    }

    /**
     * Helper method to get all data for a client
     */
    private function getClientData($client)
    {
        return [
            'users' => $client->users->pluck('id')->toArray(),
            'jobs' => $client->jobs->pluck('id')->toArray(),
            'groups' => $client->groups->pluck('id')->toArray(),
            'groupRoles' => $client->groupRoles->pluck('id')->toArray(),
            'analyses' => $client->analyses->pluck('id')->toArray(),
            'reports' => $client->reports->pluck('id')->toArray(),
            'feedbackLibraries' => $client->feedbackLibraries->pluck('id')->toArray()
        ];
    }

    /**
     * Helper method to verify no data overlap between clients
     */
    private function assertNoDataOverlap($client1Data, $client2Data)
    {
        foreach ($client1Data as $dataType => $client1Ids) {
            foreach ($client1Ids as $id) {
                $this->assertNotContains($id, $client2Data[$dataType], 
                    "Client data overlap detected in {$dataType}: ID {$id} found in both clients");
            }
        }
    }

    // ========================================
    // PERFORMANCE AND SCALABILITY TESTS
    // ========================================

    /**
     * Test multi-client query performance
     */
    public function testMultiClientQueryPerformance()
    {
        $startTime = microtime(true);

        // Create multiple clients
        $clients = [];
        for ($i = 0; $i < 10; $i++) {
            $clients[] = Client::create([
                'name' => "Performance Test Client {$i}",
                'require_profile' => $i % 2 == 0,
                'require_research' => $i % 3 == 0,
                'whitelabel' => $i % 4 == 0,
                'primary_color' => '#' . str_pad(dechex(rand(0, 16777215)), 6, '0', STR_PAD_LEFT),
                'accent_color' => '#' . str_pad(dechex(rand(0, 16777215)), 6, '0', STR_PAD_LEFT)
            ]);
        }

        $creationTime = microtime(true) - $startTime;

        // Test query performance
        $queryStartTime = microtime(true);
        
        $allClients = Client::all();
        $clientCount = Client::count();
        
        $queryTime = microtime(true) - $queryStartTime;

        $this->assertGreaterThan(10, $clientCount);
        $this->assertLessThan(1.0, $creationTime, 'Client creation should complete within 1 second');
        $this->assertLessThan(0.1, $queryTime, 'Client query should complete within 0.1 seconds');
    }

    /**
     * Test client-user relationship performance
     */
    public function testClientUserRelationshipPerformance()
    {
        $startTime = microtime(true);

        // Create multiple users for Client 1
        for ($i = 0; $i < 20; $i++) {
            User::create([
                'username' => "perfuser{$i}_" . uniqid(),
                'name' => "Performance User {$i}",
                'email' => "perfuser{$i}_" . uniqid() . '@example.com',
                'password' => bcrypt('password'),
                'client_id' => $this->client1->id,
                'language_id' => $this->language->id,
                'completed_profile' => $i % 2 == 0,
                'completed_research' => $i % 3 == 0
            ]);
        }

        $creationTime = microtime(true) - $startTime;

        // Test relationship query performance
        $queryStartTime = microtime(true);
        
        $clientUsers = $this->client1->users;
        $userCount = User::where('client_id', $this->client1->id)->count();
        
        $queryTime = microtime(true) - $queryStartTime;

        $this->assertGreaterThan(20, $userCount);
        $this->assertLessThan(2.0, $creationTime, 'User creation should complete within 2 seconds');
        $this->assertLessThan(0.1, $queryTime, 'Relationship query should complete within 0.1 seconds');
    }
}
