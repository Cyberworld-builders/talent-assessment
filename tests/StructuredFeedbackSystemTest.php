<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\FeedbackLibrary;
use App\Client;
use App\User;
use App\Assessment;
use App\Dimension;
use App\Question;
use App\Answer;
use App\Assignment;
use App\Language;
use App\Job;
use App\Http\Controllers\FeedbackController;
use Bican\Roles\Models\Role;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class StructuredFeedbackSystemTest extends TestCase
{
    protected $user;
    protected $client;
    protected $language;
    protected $assessment;
    protected $job;
    protected $dimensions;
    protected $feedbackController;

    public function setUp()
    {
        parent::setUp();
        
        // Disable CSRF protection for tests
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);
        
        // Initialize controller
        $this->feedbackController = new FeedbackController();
        
        // Create test data
        $this->language = Language::firstOrCreate([
            'name' => 'English',
            'native_name' => 'English',
            'code' => 'en'
        ]);
        
        $this->client = Client::firstOrCreate([
            'name' => 'Test Client for Structured Feedback',
            'require_profile' => true,
            'require_research' => true
        ]);

        $this->job = Job::firstOrCreate([
            'name' => 'Test Job Position for Structured Feedback',
            'slug' => 'test-job-structured-feedback-' . uniqid(),
            'client_id' => $this->client->id
        ]);
        
        // Ensure roles exist for testing
        $this->createRolesIfNeeded();
        
        // Create test user with admin role
        $this->user = User::create([
            'username' => 'structuredadmin_' . uniqid(),
            'name' => 'Structured Feedback Test Admin',
            'email' => 'structuredadmin_' . uniqid() . '@example.com',
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
            'name' => 'Test Assessment for Structured Feedback',
            'description' => 'Assessment for testing structured feedback functionality',
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

        // Create dimensions for feedback testing
        $this->dimensions = [];
        
        // Creative Problem Solving dimension
        $creativeProblemSolvingDimension = Dimension::create([
            'name' => 'Creative Problem Solving',
            'code' => 'CPS',
            'parent' => 0,
            'assessment_id' => $this->assessment->id
        ]);
        $this->dimensions['creative-problem-solving'] = $creativeProblemSolvingDimension;

        // Leadership Adaptability dimension
        $leadershipAdaptabilityDimension = Dimension::create([
            'name' => 'Leadership Adaptability',
            'code' => 'LA',
            'parent' => 0,
            'assessment_id' => $this->assessment->id
        ]);
        $this->dimensions['leadership-adaptability'] = $leadershipAdaptabilityDimension;

        // Collaboration dimension
        $collaborationDimension = Dimension::create([
            'name' => 'Collaboration',
            'code' => 'COLL',
            'parent' => 0,
            'assessment_id' => $this->assessment->id
        ]);
        $this->dimensions['collaboration'] = $collaborationDimension;
    }

    // ========================================
    // STRUCTURED FEEDBACK INTERFACE TESTS
    // ========================================

    /**
     * Test the new structured feedback interface index page
     */
    public function testStructuredFeedbackIndexPage()
    {
        $this->be($this->user);

        $response = $this->call('GET', 'dashboard/feedback');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('Feedback Libraries', $response->getContent());
        $this->assertContains('Involved-360', $response->getContent());
        $this->assertContains('Creative Problem Solving', $response->getContent());
        $this->assertContains('Leadership Adaptability', $response->getContent());
        $this->assertContains('Collaboration', $response->getContent());
    }

    /**
     * Test saving structured feedback data
     */
    public function testSaveStructuredFeedback()
    {
        $this->be($this->user);

        $structuredData = [
            'library_type' => 'involved-360',
            'name' => 'Test Structured Library',
            'dimensions' => [
                'creative-problem-solving' => [
                    'high' => 'Excellent creative problem-solving skills. You demonstrate innovative thinking and approach challenges with fresh perspectives.',
                    'medium' => 'Good creative problem-solving abilities. Continue developing your innovative thinking skills.',
                    'low' => 'Creative problem-solving skills need development. Focus on thinking outside the box and exploring multiple solutions.'
                ],
                'leadership-adaptability' => [
                    'high' => 'Outstanding leadership adaptability. You adjust your leadership style effectively based on team needs and situations.',
                    'medium' => 'Good leadership adaptability. Continue working on flexibility in your leadership approach.',
                    'low' => 'Leadership adaptability needs improvement. Focus on developing flexibility and situational awareness.'
                ],
                'collaboration' => [
                    'high' => 'Exceptional collaboration skills. You work effectively with diverse teams and build strong working relationships.',
                    'medium' => 'Good collaboration abilities. Continue developing your teamwork and communication skills.',
                    'low' => 'Collaboration skills need development. Focus on building relationships and improving team communication.'
                ]
            ]
        ];

        $response = $this->call('POST', 'dashboard/feedback/save', $structuredData);

        $this->assertEquals(200, $response->getStatusCode());
        
        $responseData = json_decode($response->getContent(), true);
        $this->assertTrue($responseData['success']);
        $this->assertEquals('Feedback saved successfully.', $responseData['message']);

        // Verify library was created/updated
        $library = FeedbackLibrary::where('name', 'Test Structured Library')->first();
        $this->assertNotNull($library);
        $this->assertEquals('Test Structured Library', $library->name);
        $this->assertArrayHasKey('creative-problem-solving', $library->feedback['dimensions']);
        $this->assertArrayHasKey('leadership-adaptability', $library->feedback['dimensions']);
        $this->assertArrayHasKey('collaboration', $library->feedback['dimensions']);
    }

    /**
     * Test getting feedback library by type
     */
    public function testGetFeedbackLibraryByType()
    {
        // Create a test library first
        $feedbackData = [
            'library_type' => 'involved-360',
            'dimensions' => [
                'creative-problem-solving' => [
                    'high' => 'High performance in creative problem solving',
                    'medium' => 'Medium performance in creative problem solving',
                    'low' => 'Low performance in creative problem solving'
                ]
            ]
        ];

        $library = FeedbackLibrary::create([
            'name' => 'Test Library for Type Retrieval',
            'feedback' => $feedbackData
        ]);

        $this->be($this->user);

        $response = $this->call('GET', 'dashboard/feedback/type/involved-360');

        $this->assertEquals(200, $response->getStatusCode());
        
        $responseData = json_decode($response->getContent(), true);
        $this->assertTrue($responseData['success']);
        $this->assertEquals($library->id, $responseData['library']['id']);
        $this->assertEquals('Test Library for Type Retrieval', $responseData['library']['name']);
    }

    /**
     * Test getting non-existent feedback library by type
     */
    public function testGetNonExistentFeedbackLibraryByType()
    {
        $this->be($this->user);

        $response = $this->call('GET', 'dashboard/feedback/type/non-existent-type');

        $this->assertEquals(404, $response->getStatusCode());
        
        $responseData = json_decode($response->getContent(), true);
        $this->assertFalse($responseData['success']);
        $this->assertEquals('Library not found', $responseData['message']);
    }

    /**
     * Test validation of structured feedback data
     */
    public function testStructuredFeedbackValidation()
    {
        $this->be($this->user);

        // Test missing library_type
        $response = $this->call('POST', 'dashboard/feedback/save', [
            'name' => 'Test Library',
            'dimensions' => []
        ]);

        $this->assertEquals(422, $response->getStatusCode());

        // Test missing name
        $response = $this->call('POST', 'dashboard/feedback/save', [
            'library_type' => 'involved-360',
            'dimensions' => []
        ]);

        $this->assertEquals(422, $response->getStatusCode());

        // Test missing dimensions
        $response = $this->call('POST', 'dashboard/feedback/save', [
            'library_type' => 'involved-360',
            'name' => 'Test Library'
        ]);

        $this->assertEquals(422, $response->getStatusCode());
    }

    /**
     * Test updating existing structured feedback library
     */
    public function testUpdateStructuredFeedbackLibrary()
    {
        // Create initial library
        $initialData = [
            'library_type' => 'involved-360',
            'dimensions' => [
                'creative-problem-solving' => [
                    'high' => 'Initial high feedback',
                    'medium' => 'Initial medium feedback',
                    'low' => 'Initial low feedback'
                ]
            ]
        ];

        $library = FeedbackLibrary::create([
            'name' => 'Initial Library Name',
            'feedback' => $initialData
        ]);

        $this->be($this->user);

        // Update the library
        $updatedData = [
            'library_type' => 'involved-360',
            'name' => 'Updated Library Name',
            'dimensions' => [
                'creative-problem-solving' => [
                    'high' => 'Updated high feedback',
                    'medium' => 'Updated medium feedback',
                    'low' => 'Updated low feedback'
                ],
                'leadership-adaptability' => [
                    'high' => 'New leadership high feedback',
                    'medium' => 'New leadership medium feedback',
                    'low' => 'New leadership low feedback'
                ]
            ]
        ];

        $response = $this->call('POST', 'dashboard/feedback/save', $updatedData);

        $this->assertEquals(200, $response->getStatusCode());
        
        $responseData = json_decode($response->getContent(), true);
        $this->assertTrue($responseData['success']);

        // Verify library was updated
        $updatedLibrary = FeedbackLibrary::find($library->id);
        $this->assertEquals('Updated Library Name', $updatedLibrary->name);
        $this->assertArrayHasKey('leadership-adaptability', $updatedLibrary->feedback['dimensions']);
        $this->assertEquals('Updated high feedback', $updatedLibrary->feedback['dimensions']['creative-problem-solving']['high']);
    }

    // ========================================
    // FEEDBACK SERVICE INTEGRATION TESTS
    // ========================================

    /**
     * Test feedback service integration with structured data
     */
    public function testFeedbackServiceIntegration()
    {
        // Create structured feedback library
        $feedbackData = [
            'library_type' => 'involved-360',
            'dimensions' => [
                'creative-problem-solving' => [
                    'high' => 'Excellent creative problem-solving skills. You demonstrate innovative thinking.',
                    'medium' => 'Good creative problem-solving abilities. Continue developing your skills.',
                    'low' => 'Creative problem-solving skills need development. Focus on innovative thinking.'
                ],
                'leadership-adaptability' => [
                    'high' => 'Outstanding leadership adaptability. You adjust your style effectively.',
                    'medium' => 'Good leadership adaptability. Continue working on flexibility.',
                    'low' => 'Leadership adaptability needs improvement. Focus on flexibility.'
                ]
            ]
        ];

        $library = FeedbackLibrary::create([
            'name' => 'Service Integration Test Library',
            'feedback' => $feedbackData
        ]);

        // Test user
        $user = User::create([
            'username' => 'serviceuser_' . uniqid(),
            'name' => 'Service Integration User',
            'email' => 'serviceuser_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'client_id' => $this->client->id,
            'language_id' => $this->language->id
        ]);

        // Test scores
        $scores = [
            'creative-problem-solving' => 85, // High performance
            'leadership-adaptability' => 65   // Medium performance
        ];

        // Test feedback generation
        $feedbackService = app('App\Services\FeedbackService');
        $generatedFeedback = $feedbackService->generateFeedback($user, $this->assessment, $scores);

        $this->assertNotEmpty($generatedFeedback);
        $this->assertArrayHasKey('creative-problem-solving', $generatedFeedback);
        $this->assertArrayHasKey('leadership-adaptability', $generatedFeedback);
        
        // Verify high performance feedback for creative problem solving
        $this->assertContains('Excellent creative problem-solving skills', $generatedFeedback['creative-problem-solving']['feedback']);
        
        // Verify medium performance feedback for leadership adaptability
        $this->assertContains('Good leadership adaptability', $generatedFeedback['leadership-adaptability']['feedback']);
    }

    /**
     * Test feedback generation with missing dimensions
     */
    public function testFeedbackGenerationWithMissingDimensions()
    {
        // Create library with limited dimensions
        $feedbackData = [
            'library_type' => 'involved-360',
            'dimensions' => [
                'creative-problem-solving' => [
                    'high' => 'High creative problem solving feedback',
                    'medium' => 'Medium creative problem solving feedback',
                    'low' => 'Low creative problem solving feedback'
                ]
            ]
        ];

        $library = FeedbackLibrary::create([
            'name' => 'Limited Dimensions Library',
            'feedback' => $feedbackData
        ]);

        $user = User::create([
            'username' => 'limiteduser_' . uniqid(),
            'name' => 'Limited Dimensions User',
            'email' => 'limiteduser_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'client_id' => $this->client->id,
            'language_id' => $this->language->id
        ]);

        // Test scores including dimensions not in library
        $scores = [
            'creative-problem-solving' => 85,
            'leadership-adaptability' => 70, // Not in library
            'collaboration' => 60            // Not in library
        ];

        $feedbackService = app('App\Services\FeedbackService');
        $generatedFeedback = $feedbackService->generateFeedback($user, $this->assessment, $scores);

        // Should have feedback for all dimensions (using default for missing ones)
        $this->assertArrayHasKey('creative-problem-solving', $generatedFeedback);
        $this->assertArrayHasKey('leadership-adaptability', $generatedFeedback);
        $this->assertArrayHasKey('collaboration', $generatedFeedback);
    }

    // ========================================
    // UI INTERACTION TESTS
    // ========================================

    /**
     * Test AJAX save functionality
     */
    public function testAjaxSaveFunctionality()
    {
        $this->be($this->user);

        $ajaxData = [
            'library_type' => 'involved-360',
            'name' => 'AJAX Test Library',
            'dimensions' => [
                'creative-problem-solving' => [
                    'high' => 'AJAX high feedback',
                    'medium' => 'AJAX medium feedback',
                    'low' => 'AJAX low feedback'
                ]
            ]
        ];

        $response = $this->call('POST', 'dashboard/feedback/save', $ajaxData);

        $this->assertEquals(200, $response->getStatusCode());
        
        $responseData = json_decode($response->getContent(), true);
        $this->assertTrue($responseData['success']);
        $this->assertEquals('Feedback saved successfully.', $responseData['message']);

        // Verify library was created
        $library = FeedbackLibrary::where('name', 'AJAX Test Library')->first();
        $this->assertNotNull($library);
        $this->assertEquals('AJAX Test Library', $library->name);
    }

    /**
     * Test library type switching
     */
    public function testLibraryTypeSwitching()
    {
        // Create libraries for different types
        $involved360Data = [
            'library_type' => 'involved-360',
            'dimensions' => [
                'creative-problem-solving' => [
                    'high' => 'Involved-360 high feedback',
                    'medium' => 'Involved-360 medium feedback',
                    'low' => 'Involved-360 low feedback'
                ]
            ]
        ];

        $involvedLeaderData = [
            'library_type' => 'involved-leader',
            'dimensions' => [
                'leadership' => [
                    'high' => 'Involved-Leader high feedback',
                    'medium' => 'Involved-Leader medium feedback',
                    'low' => 'Involved-Leader low feedback'
                ]
            ]
        ];

        FeedbackLibrary::create([
            'name' => 'Involved-360 Library',
            'feedback' => $involved360Data
        ]);

        FeedbackLibrary::create([
            'name' => 'Involved-Leader Library',
            'feedback' => $involvedLeaderData
        ]);

        $this->be($this->user);

        // Test getting involved-360 library
        $response = $this->call('GET', 'dashboard/feedback/type/involved-360');
        $this->assertEquals(200, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals('Involved-360 Library', $responseData['library']['name']);

        // Test getting involved-leader library
        $response = $this->call('GET', 'dashboard/feedback/type/involved-leader');
        $this->assertEquals(200, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals('Involved-Leader Library', $responseData['library']['name']);
    }

    // ========================================
    // PERFORMANCE AND EDGE CASE TESTS
    // ========================================

    /**
     * Test large feedback library handling
     */
    public function testLargeFeedbackLibraryHandling()
    {
        $this->be($this->user);

        // Create a large feedback library with many dimensions
        $largeDimensions = [];
        for ($i = 1; $i <= 20; $i++) {
            $largeDimensions["dimension-{$i}"] = [
                'high' => "High feedback for dimension {$i}. This is a longer feedback message to test handling of substantial content.",
                'medium' => "Medium feedback for dimension {$i}. This is a longer feedback message to test handling of substantial content.",
                'low' => "Low feedback for dimension {$i}. This is a longer feedback message to test handling of substantial content."
            ];
        }

        $largeData = [
            'library_type' => 'large-test',
            'name' => 'Large Test Library',
            'dimensions' => $largeDimensions
        ];

        $response = $this->call('POST', 'dashboard/feedback/save', $largeData);

        $this->assertEquals(200, $response->getStatusCode());
        
        $responseData = json_decode($response->getContent(), true);
        $this->assertTrue($responseData['success']);

        // Verify all dimensions were saved
        $library = FeedbackLibrary::where('name', 'Large Test Library')->first();
        $this->assertNotNull($library);
        $this->assertCount(20, $library->feedback['dimensions']);
    }

    /**
     * Test special characters in feedback content
     */
    public function testSpecialCharactersInFeedback()
    {
        $this->be($this->user);

        $specialCharData = [
            'library_type' => 'special-chars',
            'name' => 'Special Characters Library',
            'dimensions' => [
                'test-dimension' => [
                    'high' => 'High feedback with special chars: éñüñ, "quotes", \'apostrophes\', <tags>, & symbols, and émojis 🚀',
                    'medium' => 'Medium feedback with special chars: éñüñ, "quotes", \'apostrophes\', <tags>, & symbols, and émojis 🚀',
                    'low' => 'Low feedback with special chars: éñüñ, "quotes", \'apostrophes\', <tags>, & symbols, and émojis 🚀'
                ]
            ]
        ];

        $response = $this->call('POST', 'dashboard/feedback/save', $specialCharData);

        $this->assertEquals(200, $response->getStatusCode());
        
        $responseData = json_decode($response->getContent(), true);
        $this->assertTrue($responseData['success']);

        // Verify special characters were preserved
        $library = FeedbackLibrary::where('name', 'Special Characters Library')->first();
        $this->assertNotNull($library);
        $this->assertContains('éñüñ', $library->feedback['dimensions']['test-dimension']['high']);
        $this->assertContains('🚀', $library->feedback['dimensions']['test-dimension']['high']);
    }

    /**
     * Test concurrent library updates
     */
    public function testConcurrentLibraryUpdates()
    {
        // Create initial library
        $initialData = [
            'library_type' => 'concurrent-test',
            'dimensions' => [
                'test-dimension' => [
                    'high' => 'Initial high feedback',
                    'medium' => 'Initial medium feedback',
                    'low' => 'Initial low feedback'
                ]
            ]
        ];

        $library = FeedbackLibrary::create([
            'name' => 'Concurrent Test Library',
            'feedback' => $initialData
        ]);

        $this->be($this->user);

        // Simulate concurrent updates
        $update1 = [
            'library_type' => 'concurrent-test',
            'name' => 'Updated by User 1',
            'dimensions' => [
                'test-dimension' => [
                    'high' => 'Updated high feedback by User 1',
                    'medium' => 'Updated medium feedback by User 1',
                    'low' => 'Updated low feedback by User 1'
                ]
            ]
        ];

        $update2 = [
            'library_type' => 'concurrent-test',
            'name' => 'Updated by User 2',
            'dimensions' => [
                'test-dimension' => [
                    'high' => 'Updated high feedback by User 2',
                    'medium' => 'Updated medium feedback by User 2',
                    'low' => 'Updated low feedback by User 2'
                ]
            ]
        ];

        // Execute updates
        $response1 = $this->call('POST', 'dashboard/feedback/save', $update1);
        $response2 = $this->call('POST', 'dashboard/feedback/save', $update2);

        $this->assertEquals(200, $response1->getStatusCode());
        $this->assertEquals(200, $response2->getStatusCode());

        // Verify final state (should be the last update)
        $finalLibrary = FeedbackLibrary::find($library->id);
        $this->assertNotNull($finalLibrary);
        // The exact final state depends on timing, but it should be one of the updates
        $this->assertTrue(
            $finalLibrary->name === 'Updated by User 1' || 
            $finalLibrary->name === 'Updated by User 2'
        );
    }
}
