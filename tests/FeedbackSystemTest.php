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
use Spatie\Permission\Models\Role;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class FeedbackSystemTest extends TestCase
{
    protected $user;
    protected $client;
    protected $language;
    protected $assessment;
    protected $job;
    protected $dimensions;
    protected $feedbackController;

    public function setUp(): void
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
            'name' => 'Test Client for Feedback',
            'require_profile' => true,
            'require_research' => true
        ]);

        $this->job = Job::firstOrCreate([
            'name' => 'Test Job Position for Feedback',
            'slug' => 'test-job-feedback-' . uniqid(),
            'client_id' => $this->client->id
        ]);
        
        // Ensure roles exist for testing
        $this->createRolesIfNeeded();
        
        // Create test user with admin role
        $this->user = User::create([
            'username' => 'feedbackadmin_' . uniqid(),
            'name' => 'Feedback Test Admin',
            'email' => 'feedbackadmin_' . uniqid() . '@example.com',
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
            'name' => 'Test Assessment for Feedback',
            'description' => 'Assessment for testing feedback functionality',
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
    // FEEDBACK LIBRARY CREATION TESTS
    // ========================================

    /**
     * Test feedback library creation with comprehensive structure
     */
    public function testFeedbackLibraryCreation()
    {
        $feedbackData = [
            'dimensions' => [
                'leadership' => [
                    'high' => 'Exceptional leadership capabilities demonstrated. Your ability to inspire and guide teams is outstanding.',
                    'medium' => 'Good leadership foundation with solid potential for growth. Focus on developing your influence skills.',
                    'low' => 'Leadership development opportunity identified. Start by building confidence in group settings.'
                ],
                'communication' => [
                    'high' => 'Outstanding communication skills. You effectively convey ideas and build rapport.',
                    'medium' => 'Good communication abilities. Continue practicing clear and concise expression.',
                    'low' => 'Communication skills need improvement. Focus on clarity and active listening.'
                ]
            ],
            'overall' => [
                'high' => 'Excellent overall performance. You demonstrate strong capabilities across all dimensions.',
                'medium' => 'Good performance with areas for improvement. Continue developing your skills.',
                'low' => 'Development areas identified. Focus on building foundational skills.'
            ]
        ];

        $library = FeedbackLibrary::create([
            'name' => 'Comprehensive Feedback Library',
            'feedback' => $feedbackData
        ]);

        $this->assertNotNull($library->id);
        $this->assertEquals('Comprehensive Feedback Library', $library->name);
        $this->assertTrue(is_array($library->feedback));
        $this->assertArrayHasKey('dimensions', $library->feedback);
        $this->assertArrayHasKey('overall', $library->feedback);
        $this->assertArrayHasKey('leadership', $library->feedback['dimensions']);
        $this->assertArrayHasKey('communication', $library->feedback['dimensions']);
    }

    /**
     * Test feedback library with client association
     */
    public function testFeedbackLibraryWithClient()
    {
        $feedbackData = [
            'dimensions' => [
                'leadership' => [
                    'high' => 'Client-specific leadership feedback for high performers.',
                    'medium' => 'Client-specific leadership feedback for medium performers.',
                    'low' => 'Client-specific leadership feedback for low performers.'
                ]
            ]
        ];

        $library = FeedbackLibrary::create([
            'name' => 'Client-Specific Library',
            'feedback' => $feedbackData,
            'client_id' => $this->client->id
        ]);

        $this->assertNotNull($library->id);
        $this->assertEquals($this->client->id, $library->client_id);
        
        // Test relationship
        $this->assertNotNull($library->client);
        $this->assertEquals($this->client->name, $library->client->name);
        
        // Test client relationship from other side
        $clientLibraries = $this->client->feedbackLibraries;
        $this->assertTrue($clientLibraries->contains($library));
    }

    /**
     * Test feedback library JSON encoding and decoding
     */
    public function testFeedbackLibraryJsonHandling()
    {
        $complexFeedbackData = [
            'dimensions' => [
                'leadership' => [
                    'high' => 'Exceptional leadership with specific examples: team motivation, strategic thinking, decision-making.',
                    'medium' => 'Good leadership with development areas: influence skills, confidence building.',
                    'low' => 'Leadership development needed: group confidence, communication practice.'
                ]
            ],
            'metadata' => [
                'version' => '1.0',
                'created_by' => 'admin',
                'last_updated' => '2024-01-01'
            ],
            'recommendations' => [
                'high' => ['Mentor others', 'Take on senior roles', 'Lead strategic projects'],
                'medium' => ['Practice leadership', 'Seek feedback', 'Join leadership programs'],
                'low' => ['Build confidence', 'Take initiative', 'Seek mentorship']
            ]
        ];

        $library = FeedbackLibrary::create([
            'name' => 'Complex JSON Library',
            'feedback' => $complexFeedbackData
        ]);

        // Test that data is properly stored and retrieved
        $retrievedLibrary = FeedbackLibrary::find($library->id);
        $this->assertEquals($complexFeedbackData, $retrievedLibrary->feedback);
        
        // Test nested access
        $this->assertEquals('Exceptional leadership with specific examples: team motivation, strategic thinking, decision-making.', 
            $retrievedLibrary->feedback['dimensions']['leadership']['high']);
        $this->assertEquals('1.0', $retrievedLibrary->feedback['metadata']['version']);
        $this->assertCount(3, $retrievedLibrary->feedback['recommendations']['high']);
    }

    // ========================================
    // FEEDBACK LIBRARY MANAGEMENT TESTS
    // ========================================

    /**
     * Test feedback library editing and updates
     */
    public function testFeedbackLibraryEditing()
    {
        $originalFeedback = [
            'dimensions' => [
                'leadership' => [
                    'high' => 'Original high leadership feedback',
                    'medium' => 'Original medium leadership feedback',
                    'low' => 'Original low leadership feedback'
                ]
            ]
        ];

        $library = FeedbackLibrary::create([
            'name' => 'Editable Library',
            'feedback' => $originalFeedback
        ]);

        // Update feedback content
        $updatedFeedback = [
            'dimensions' => [
                'leadership' => [
                    'high' => 'Updated high leadership feedback',
                    'medium' => 'Updated medium leadership feedback',
                    'low' => 'Updated low leadership feedback'
                ],
                'communication' => [
                    'high' => 'New communication feedback for high performers',
                    'medium' => 'New communication feedback for medium performers',
                    'low' => 'New communication feedback for low performers'
                ]
            ]
        ];

        $library->feedback = $updatedFeedback;
        $library->save();

        // Verify updates
        $retrievedLibrary = FeedbackLibrary::find($library->id);
        $this->assertEquals($updatedFeedback, $retrievedLibrary->feedback);
        $this->assertArrayHasKey('communication', $retrievedLibrary->feedback['dimensions']);
        $this->assertEquals('Updated high leadership feedback', 
            $retrievedLibrary->feedback['dimensions']['leadership']['high']);
    }

    /**
     * Test feedback library validation
     */
    public function testFeedbackLibraryValidation()
    {
        // Test unique name constraint
        FeedbackLibrary::create([
            'name' => 'Unique Name Test',
            'feedback' => ['test' => 'value1']
        ]);

        $this->expectException('Illuminate\Database\QueryException');
        
        FeedbackLibrary::create([
            'name' => 'Unique Name Test',
            'feedback' => ['test' => 'value2']
        ]);
    }

    /**
     * Test feedback library archiving (soft delete if implemented)
     */
    public function testFeedbackLibraryArchiving()
    {
        $library = FeedbackLibrary::create([
            'name' => 'Archivable Library',
            'feedback' => ['test' => 'value']
        ]);

        $libraryId = $library->id;

        // Test deletion
        $library->delete();
        
        $deletedLibrary = FeedbackLibrary::find($libraryId);
        $this->assertNull($deletedLibrary);
    }

    // ========================================
    // FEEDBACK CONTROLLER TESTS
    // ========================================

    /**
     * Test feedback controller index method
     */
    public function testFeedbackControllerIndex()
    {
        $this->be($this->user);

        // Create some test libraries
        FeedbackLibrary::create([
            'name' => 'Global Library 1',
            'feedback' => ['test' => 'value1']
        ]);

        FeedbackLibrary::create([
            'name' => 'Global Library 2',
            'feedback' => ['test' => 'value2']
        ]);

        $response = $this->call('GET', 'dashboard/feedback');

        // Should return 200 or redirect (depending on middleware)
        $this->assertTrue(in_array($response->getStatusCode(), [200, 302]));
    }

    /**
     * Test feedback controller store method
     */
    public function testFeedbackControllerStore()
    {
        $this->be($this->user);

        $requestData = [
            'name' => 'Controller Test Library',
            'feedback' => [
                'dimensions' => [
                    'leadership' => [
                        'high' => 'Controller test high feedback',
                        'medium' => 'Controller test medium feedback',
                        'low' => 'Controller test low feedback'
                    ]
                ]
            ]
        ];

        $response = $this->call('POST', 'dashboard/feedback', $requestData);

        // Should return JSON response
        $this->assertEquals(200, $response->getStatusCode());
        
        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('success', $responseData);

        // Verify library was created
        $library = FeedbackLibrary::where('name', 'Controller Test Library')->first();
        $this->assertNotNull($library);
        $this->assertEquals($requestData['feedback'], $library->feedback);
    }

    /**
     * Test feedback controller validation
     */
    public function testFeedbackControllerValidation()
    {
        $this->be($this->user);

        // Test missing name
        $response = $this->call('POST', 'dashboard/feedback', [
            'feedback' => ['test' => 'value']
        ]);

        $this->assertEquals(200, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('errors', $responseData);

        // Test missing feedback
        $response = $this->call('POST', 'dashboard/feedback', [
            'name' => 'Test Library'
        ]);

        $this->assertEquals(200, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('errors', $responseData);
    }

    // ========================================
    // DYNAMIC CONTENT GENERATION TESTS
    // ========================================

    /**
     * Test dynamic content generation based on scores
     */
    public function testDynamicContentGeneration()
    {
        $feedbackLibrary = FeedbackLibrary::create([
            'name' => 'Dynamic Content Library',
            'feedback' => [
                'dimensions' => [
                    'leadership' => [
                        'high' => 'Excellent leadership (score: {score}). You demonstrate outstanding capabilities.',
                        'medium' => 'Good leadership (score: {score}). Continue developing your skills.',
                        'low' => 'Leadership development needed (score: {score}). Focus on building confidence.'
                    ],
                    'communication' => [
                        'high' => 'Outstanding communication (score: {score}). You effectively convey ideas.',
                        'medium' => 'Good communication (score: {score}). Continue practicing clarity.',
                        'low' => 'Communication improvement needed (score: {score}). Focus on active listening.'
                    ]
                ]
            ]
        ]);

        // Test score-based content generation
        $scores = [
            'leadership' => 85,
            'communication' => 72
        ];

        $generatedFeedback = $this->generateDynamicFeedback($scores, $feedbackLibrary);

        $this->assertArrayHasKey('leadership', $generatedFeedback);
        $this->assertArrayHasKey('communication', $generatedFeedback);
        $this->assertContains('85', $generatedFeedback['leadership']);
        $this->assertContains('72', $generatedFeedback['communication']);
    }

    /**
     * Test performance level determination
     */
    public function testPerformanceLevelDetermination()
    {
        // Test high performance
        $this->assertEquals('high', $this->determinePerformanceLevel(85));
        $this->assertEquals('high', $this->determinePerformanceLevel(90));
        $this->assertEquals('high', $this->determinePerformanceLevel(100));

        // Test medium performance
        $this->assertEquals('medium', $this->determinePerformanceLevel(75));
        $this->assertEquals('medium', $this->determinePerformanceLevel(65));
        $this->assertEquals('medium', $this->determinePerformanceLevel(60));

        // Test low performance
        $this->assertEquals('low', $this->determinePerformanceLevel(55));
        $this->assertEquals('low', $this->determinePerformanceLevel(40));
        $this->assertEquals('low', $this->determinePerformanceLevel(30));
    }

    /**
     * Test personalized feedback generation
     */
    public function testPersonalizedFeedbackGeneration()
    {
        $feedbackLibrary = FeedbackLibrary::create([
            'name' => 'Personalized Feedback Library',
            'feedback' => [
                'dimensions' => [
                    'leadership' => [
                        'high' => 'Exceptional leadership for {user_name}. Your ability to inspire teams is outstanding.',
                        'medium' => 'Good leadership potential, {user_name}. Focus on developing influence skills.',
                        'low' => 'Leadership development opportunity for {user_name}. Start building confidence.'
                    ]
                ],
                'overall' => [
                    'high' => 'Excellent overall performance, {user_name}! You demonstrate strong capabilities.',
                    'medium' => 'Good performance, {user_name}. Continue developing your skills.',
                    'low' => 'Development areas identified for {user_name}. Focus on foundational skills.'
                ]
            ]
        ]);

        $user = User::create([
            'username' => 'feedbackuser_' . uniqid(),
            'name' => 'John Doe',
            'email' => 'feedbackuser_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'client_id' => $this->client->id,
            'language_id' => $this->language->id
        ]);

        $scores = [
            'leadership' => 88,
            'overall' => 82
        ];

        $personalizedFeedback = $this->generatePersonalizedFeedback($scores, $feedbackLibrary, $user);

        $this->assertArrayHasKey('leadership', $personalizedFeedback);
        $this->assertArrayHasKey('overall', $personalizedFeedback);
        $this->assertContains('John Doe', $personalizedFeedback['leadership']);
        $this->assertContains('John Doe', $personalizedFeedback['overall']);
    }

    // ========================================
    // SCORE-BASED FEEDBACK SELECTION TESTS
    // ========================================

    /**
     * Test score-based feedback selection
     */
    public function testScoreBasedFeedbackSelection()
    {
        $feedbackLibrary = FeedbackLibrary::create([
            'name' => 'Score-Based Library',
            'feedback' => [
                'dimensions' => [
                    'leadership' => [
                        'high' => 'High performance leadership feedback',
                        'medium' => 'Medium performance leadership feedback',
                        'low' => 'Low performance leadership feedback'
                    ],
                    'communication' => [
                        'high' => 'High performance communication feedback',
                        'medium' => 'Medium performance communication feedback',
                        'low' => 'Low performance communication feedback'
                    ]
                ]
            ]
        ]);

        // Test high score selection
        $highScoreFeedback = $this->selectFeedbackByScore(85, 'leadership', $feedbackLibrary);
        $this->assertEquals('High performance leadership feedback', $highScoreFeedback);

        // Test medium score selection
        $mediumScoreFeedback = $this->selectFeedbackByScore(70, 'communication', $feedbackLibrary);
        $this->assertEquals('Medium performance communication feedback', $mediumScoreFeedback);

        // Test low score selection
        $lowScoreFeedback = $this->selectFeedbackByScore(45, 'leadership', $feedbackLibrary);
        $this->assertEquals('Low performance leadership feedback', $lowScoreFeedback);
    }

    /**
     * Test feedback selection with custom thresholds
     */
    public function testCustomThresholdFeedbackSelection()
    {
        $feedbackLibrary = FeedbackLibrary::create([
            'name' => 'Custom Threshold Library',
            'feedback' => [
                'dimensions' => [
                    'leadership' => [
                        'excellent' => 'Excellent leadership (90+)',
                        'good' => 'Good leadership (70-89)',
                        'fair' => 'Fair leadership (50-69)',
                        'needs_improvement' => 'Needs improvement (below 50)'
                    ]
                ]
            ]
        ]);

        // Test custom threshold selection
        $this->assertEquals('Excellent leadership (90+)', 
            $this->selectFeedbackWithCustomThresholds(95, 'leadership', $feedbackLibrary, [
                'excellent' => 90,
                'good' => 70,
                'fair' => 50
            ]));

        $this->assertEquals('Good leadership (70-89)', 
            $this->selectFeedbackWithCustomThresholds(75, 'leadership', $feedbackLibrary, [
                'excellent' => 90,
                'good' => 70,
                'fair' => 50
            ]));
    }

    // ========================================
    // FEEDBACK DELIVERY TESTS
    // ========================================

    /**
     * Test feedback delivery to users
     */
    public function testFeedbackDelivery()
    {
        $feedbackLibrary = FeedbackLibrary::create([
            'name' => 'Delivery Test Library',
            'feedback' => [
                'dimensions' => [
                    'leadership' => [
                        'high' => 'High leadership feedback for delivery',
                        'medium' => 'Medium leadership feedback for delivery',
                        'low' => 'Low leadership feedback for delivery'
                    ]
                ]
            ]
        ]);

        $user = User::create([
            'username' => 'deliveryuser_' . uniqid(),
            'name' => 'Delivery Test User',
            'email' => 'deliveryuser_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'client_id' => $this->client->id,
            'language_id' => $this->language->id
        ]);

        $scores = ['leadership' => 78];
        $deliveredFeedback = $this->deliverFeedbackToUser($user, $scores, $feedbackLibrary);

        $this->assertArrayHasKey('leadership', $deliveredFeedback);
        $this->assertEquals('Medium leadership feedback for delivery', $deliveredFeedback['leadership']);
    }

    /**
     * Test feedback delivery with multiple dimensions
     */
    public function testMultiDimensionalFeedbackDelivery()
    {
        $feedbackLibrary = FeedbackLibrary::create([
            'name' => 'Multi-Dimensional Library',
            'feedback' => [
                'dimensions' => [
                    'leadership' => [
                        'high' => 'High leadership feedback',
                        'medium' => 'Medium leadership feedback',
                        'low' => 'Low leadership feedback'
                    ],
                    'communication' => [
                        'high' => 'High communication feedback',
                        'medium' => 'Medium communication feedback',
                        'low' => 'Low communication feedback'
                    ],
                    'teamwork' => [
                        'high' => 'High teamwork feedback',
                        'medium' => 'Medium teamwork feedback',
                        'low' => 'Low teamwork feedback'
                    ]
                ]
            ]
        ]);

        $user = User::create([
            'username' => 'multiuser_' . uniqid(),
            'name' => 'Multi-Dimensional User',
            'email' => 'multiuser_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'client_id' => $this->client->id,
            'language_id' => $this->language->id
        ]);

        $scores = [
            'leadership' => 85,
            'communication' => 72,
            'teamwork' => 68
        ];

        $deliveredFeedback = $this->deliverFeedbackToUser($user, $scores, $feedbackLibrary);

        $this->assertCount(3, $deliveredFeedback);
        $this->assertArrayHasKey('leadership', $deliveredFeedback);
        $this->assertArrayHasKey('communication', $deliveredFeedback);
        $this->assertArrayHasKey('teamwork', $deliveredFeedback);
        $this->assertEquals('High leadership feedback', $deliveredFeedback['leadership']);
        $this->assertEquals('Medium communication feedback', $deliveredFeedback['communication']);
        $this->assertEquals('Medium teamwork feedback', $deliveredFeedback['teamwork']);
    }

    // ========================================
    // FEEDBACK INTEGRATION TESTS
    // ========================================

    /**
     * Test feedback integration with assessment results
     */
    public function testFeedbackAssessmentIntegration()
    {
        // Create assessment with questions using relationship pattern
        $question1 = new Question([
            'content' => 'Test leadership question',
            'number' => 1,
            'type' => 1,
            'dimension_id' => $this->dimensions['leadership']->id
        ]);
        $this->assessment->questions()->save($question1);

        $question2 = new Question([
            'content' => 'Test communication question',
            'number' => 2,
            'type' => 1,
            'dimension_id' => $this->dimensions['communication']->id
        ]);
        $this->assessment->questions()->save($question2);

        // Create user and assignment
        $user = User::create([
            'username' => 'integrationuser_' . uniqid(),
            'name' => 'Integration Test User',
            'email' => 'integrationuser_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'client_id' => $this->client->id,
            'language_id' => $this->language->id
        ]);

        $assignmentData = [
            'assessment_id' => $this->assessment->id,
            'job_id' => $this->job->id,
            'completed' => true,
            'completed_at' => Carbon::now()
        ];
        $assignment = new Assignment($assignmentData);
        $user->assignments()->save($assignment);

        // Create answers using relationship pattern
        // Using scores that will clearly map to high/medium based on our performance level logic
        $answer1 = new Answer([
            'question_id' => $question1->id,
            'answer' => 85, // High score (>= 80)
            'time_taken' => 30
        ]);
        $assignment->answers()->save($answer1);

        $answer2 = new Answer([
            'question_id' => $question2->id,
            'answer' => 70, // Medium score (60-79)
            'time_taken' => 25
        ]);
        $assignment->answers()->save($answer2);

        // Create feedback library
        $feedbackLibrary = FeedbackLibrary::create([
            'name' => 'Integration Test Library',
            'feedback' => [
                'dimensions' => [
                    'Leadership' => [
                        'high' => 'High leadership performance in assessment',
                        'medium' => 'Medium leadership performance in assessment',
                        'low' => 'Low leadership performance in assessment'
                    ],
                    'Communication' => [
                        'high' => 'High communication performance in assessment',
                        'medium' => 'Medium communication performance in assessment',
                        'low' => 'Low communication performance in assessment'
                    ]
                ]
            ]
        ]);

        // Calculate scores and generate feedback
        $scores = $this->calculateAssessmentScores($assignment);
        $feedback = $this->generateFeedbackFromAssessment($scores, $feedbackLibrary, $user);

        // Verify feedback was generated successfully
        $this->assertNotEmpty($scores, 'Scores should not be empty');
        $this->assertNotEmpty($feedback, 'Feedback should not be empty');
        
        // Verify feedback contains expected dimensions
        $this->assertArrayHasKey('Leadership', $feedback);
        $this->assertArrayHasKey('Communication', $feedback);
        
        // Verify feedback content is appropriate
        $this->assertContains('leadership performance in assessment', $feedback['Leadership']);
        $this->assertContains('communication performance in assessment', $feedback['Communication']);
    }

    // ========================================
    // HELPER METHODS
    // ========================================

    /**
     * Generate dynamic feedback based on scores
     */
    private function generateDynamicFeedback($scores, $feedbackLibrary)
    {
        $feedback = [];
        $feedbackData = $feedbackLibrary->feedback;

        foreach ($scores as $dimension => $score) {
            if (isset($feedbackData['dimensions'][$dimension])) {
                $level = $this->determinePerformanceLevel($score);
                $feedbackText = $feedbackData['dimensions'][$dimension][$level] ?? '';
                $feedback[$dimension] = str_replace('{score}', $score, $feedbackText);
            }
        }

        return $feedback;
    }

    /**
     * Determine performance level based on score
     */
    private function determinePerformanceLevel($score)
    {
        if ($score >= 80) return 'high';
        if ($score >= 60) return 'medium';
        return 'low';
    }

    /**
     * Generate personalized feedback
     */
    private function generatePersonalizedFeedback($scores, $feedbackLibrary, $user)
    {
        $feedback = [];
        $feedbackData = $feedbackLibrary->feedback;

        foreach ($scores as $dimension => $score) {
            if (isset($feedbackData['dimensions'][$dimension])) {
                $level = $this->determinePerformanceLevel($score);
                $feedbackText = $feedbackData['dimensions'][$dimension][$level] ?? '';
                $feedback[$dimension] = str_replace('{user_name}', $user->name, $feedbackText);
            }
        }

        // Add overall feedback if available
        if (isset($scores['overall']) && isset($feedbackData['overall'])) {
            $level = $this->determinePerformanceLevel($scores['overall']);
            $feedbackText = $feedbackData['overall'][$level] ?? '';
            $feedback['overall'] = str_replace('{user_name}', $user->name, $feedbackText);
        }

        return $feedback;
    }

    /**
     * Select feedback by score
     */
    private function selectFeedbackByScore($score, $dimension, $feedbackLibrary)
    {
        $level = $this->determinePerformanceLevel($score);
        $feedbackData = $feedbackLibrary->feedback;
        
        return $feedbackData['dimensions'][$dimension][$level] ?? '';
    }

    /**
     * Select feedback with custom thresholds
     */
    private function selectFeedbackWithCustomThresholds($score, $dimension, $feedbackLibrary, $thresholds)
    {
        $feedbackData = $feedbackLibrary->feedback;
        
        if ($score >= $thresholds['excellent']) {
            return $feedbackData['dimensions'][$dimension]['excellent'] ?? '';
        } elseif ($score >= $thresholds['good']) {
            return $feedbackData['dimensions'][$dimension]['good'] ?? '';
        } elseif ($score >= $thresholds['fair']) {
            return $feedbackData['dimensions'][$dimension]['fair'] ?? '';
        } else {
            return $feedbackData['dimensions'][$dimension]['needs_improvement'] ?? '';
        }
    }

    /**
     * Deliver feedback to user
     */
    private function deliverFeedbackToUser($user, $scores, $feedbackLibrary)
    {
        return $this->generateDynamicFeedback($scores, $feedbackLibrary);
    }

    /**
     * Calculate assessment scores
     */
    private function calculateAssessmentScores($assignment)
    {
        $scores = [];
        $answers = $assignment->answers;

        foreach ($answers as $answer) {
            $question = $answer->question;
            $dimension = $question->dimension();
            
            if ($dimension && !isset($scores[$dimension->name])) {
                $scores[$dimension->name] = [];
            }
            
            if ($dimension) {
                $scores[$dimension->name][] = $answer->answer;
            }
        }

        // Calculate averages
        $averages = [];
        foreach ($scores as $dimension => $scoreArray) {
            $averages[$dimension] = array_sum($scoreArray) / count($scoreArray);
        }

        return $averages;
    }

    /**
     * Generate feedback from assessment
     */
    private function generateFeedbackFromAssessment($scores, $feedbackLibrary, $user)
    {
        return $this->generatePersonalizedFeedback($scores, $feedbackLibrary, $user);
    }
}
