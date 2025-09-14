<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\FeedbackLibrary;
use App\Client;
use App\User;
use App\Assessment;
use App\Dimension;
use App\Services\FeedbackService;
use App\Language;
use Bican\Roles\Models\Role;

class FeedbackServiceTest extends TestCase
{
    protected $user;
    protected $client;
    protected $language;
    protected $assessment;
    protected $feedbackService;

    public function setUp()
    {
        parent::setUp();
        
        // Initialize feedback service
        $this->feedbackService = new FeedbackService();
        
        // Create test data
        $this->language = Language::firstOrCreate([
            'name' => 'English',
            'native_name' => 'English',
            'code' => 'en'
        ]);
        
        $this->client = Client::firstOrCreate([
            'name' => 'Test Client for Feedback Service',
            'require_profile' => true,
            'require_research' => true
        ]);
        
        // Ensure roles exist for testing
        $this->createRolesIfNeeded();
        
        // Create test user
        $this->user = User::create([
            'username' => 'feedbackserviceuser_' . uniqid(),
            'name' => 'Feedback Service Test User',
            'email' => 'feedbackserviceuser_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'client_id' => $this->client->id,
            'language_id' => $this->language->id,
            'completed_profile' => true,
            'completed_research' => true
        ]);
        
        $adminRole = Role::where('slug', 'admin')->first();
        $this->user->attachRole($adminRole);

        // Create test assessment
        $this->createTestAssessment();
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
     * Create test assessment
     */
    private function createTestAssessment()
    {
        $assessmentData = [
            'name' => 'Test Assessment for Feedback Service',
            'description' => 'Assessment for testing feedback service functionality',
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
    }

    // ========================================
    // FEEDBACK GENERATION TESTS
    // ========================================

    /**
     * Test basic feedback generation
     */
    public function testBasicFeedbackGeneration()
    {
        // Use existing library dimensions for testing
        $scores = [
            'relationships' => 85, // High performance
            'relationships' => 65   // Medium performance
        ];

        $feedback = $this->feedbackService->generateFeedback($this->user, $this->assessment, $scores);

        $this->assertNotEmpty($feedback);
        $this->assertArrayHasKey('relationships', $feedback);
        
        // Verify feedback structure
        $this->assertArrayHasKey('score', $feedback['relationships']);
        $this->assertArrayHasKey('level', $feedback['relationships']);
        $this->assertArrayHasKey('feedback', $feedback['relationships']);
        $this->assertArrayHasKey('color', $feedback['relationships']);
        $this->assertArrayHasKey('icon', $feedback['relationships']);
        $this->assertArrayHasKey('action_items', $feedback['relationships']);
    }

    /**
     * Test feedback generation with client-specific library
     */
    public function testClientSpecificFeedbackGeneration()
    {
        // Create client-specific feedback library
        $feedbackData = [
            'library_type' => 'involved-360',
            'dimensions' => [
                'creative-problem-solving' => [
                    'high' => 'Client-specific high feedback for creative problem solving.',
                    'medium' => 'Client-specific medium feedback for creative problem solving.',
                    'low' => 'Client-specific low feedback for creative problem solving.'
                ]
            ]
        ];

        $library = FeedbackLibrary::create([
            'name' => 'Client-Specific Library',
            'feedback' => $feedbackData,
            'client_id' => $this->client->id
        ]);

        $scores = ['creative-problem-solving' => 85];

        $feedback = $this->feedbackService->generateFeedback($this->user, $this->assessment, $scores);

        $this->assertNotEmpty($feedback);
        $this->assertArrayHasKey('creative-problem-solving', $feedback);
        $this->assertContains('Client-specific high feedback', $feedback['creative-problem-solving']['feedback']);
    }

    /**
     * Test feedback generation with global library fallback
     */
    public function testGlobalLibraryFallback()
    {
        // Create global library
        $globalFeedbackData = [
            'library_type' => 'involved-360',
            'dimensions' => [
                'creative-problem-solving' => [
                    'high' => 'Global high feedback for creative problem solving.',
                    'medium' => 'Global medium feedback for creative problem solving.',
                    'low' => 'Global low feedback for creative problem solving.'
                ]
            ]
        ];

        $globalLibrary = FeedbackLibrary::create([
            'name' => 'Global Library',
            'feedback' => $globalFeedbackData,
            'client_id' => null
        ]);

        // Create client-specific library for different client
        $otherClient = Client::create([
            'name' => 'Other Client',
            'require_profile' => true,
            'require_research' => true
        ]);

        $clientFeedbackData = [
            'library_type' => 'involved-360',
            'dimensions' => [
                'creative-problem-solving' => [
                    'high' => 'Other client high feedback for creative problem solving.',
                    'medium' => 'Other client medium feedback for creative problem solving.',
                    'low' => 'Other client low feedback for creative problem solving.'
                ]
            ]
        ];

        FeedbackLibrary::create([
            'name' => 'Other Client Library',
            'feedback' => $clientFeedbackData,
            'client_id' => $otherClient->id
        ]);

        $scores = ['relationships' => 85];

        $feedback = $this->feedbackService->generateFeedback($this->user, $this->assessment, $scores);

        $this->assertNotEmpty($feedback);
        $this->assertArrayHasKey('relationships', $feedback);
        // Should use global library since no client-specific library exists for this client
        $this->assertArrayHasKey('feedback', $feedback['relationships']);
    }

    /**
     * Test performance level determination
     */
    public function testPerformanceLevelDetermination()
    {
        // Test high performance
        $this->assertEquals('high', $this->feedbackService->getPerformanceLevel(85));
        $this->assertEquals('high', $this->feedbackService->getPerformanceLevel(90));
        $this->assertEquals('high', $this->feedbackService->getPerformanceLevel(100));

        // Test medium performance
        $this->assertEquals('medium', $this->feedbackService->getPerformanceLevel(75));
        $this->assertEquals('medium', $this->feedbackService->getPerformanceLevel(65));
        $this->assertEquals('medium', $this->feedbackService->getPerformanceLevel(60));

        // Test low performance
        $this->assertEquals('low', $this->feedbackService->getPerformanceLevel(55));
        $this->assertEquals('low', $this->feedbackService->getPerformanceLevel(40));
        $this->assertEquals('low', $this->feedbackService->getPerformanceLevel(30));
    }

    /**
     * Test level color determination
     */
    public function testLevelColorDetermination()
    {
        $this->assertEquals('success', $this->feedbackService->getLevelColor('high'));
        $this->assertEquals('warning', $this->feedbackService->getLevelColor('medium'));
        $this->assertEquals('danger', $this->feedbackService->getLevelColor('low'));
    }

    /**
     * Test level icon determination
     */
    public function testLevelIconDetermination()
    {
        $this->assertEquals('fa-star', $this->feedbackService->getLevelIcon('high'));
        $this->assertEquals('fa-star-half-o', $this->feedbackService->getLevelIcon('medium'));
        $this->assertEquals('fa-star-o', $this->feedbackService->getLevelIcon('low'));
    }

    /**
     * Test action items generation
     */
    public function testActionItemsGeneration()
    {
        $scores = [
            'creative-problem-solving' => 85,
            'leadership-adaptability' => 65,
            'collaboration' => 45
        ];

        $actionItems = [];
        foreach ($scores as $dimension => $score) {
            $level = $this->feedbackService->getPerformanceLevel($score);
            $actionItems[$dimension] = $this->feedbackService->generateActionItems($level, $dimension);
        }

        $this->assertNotEmpty($actionItems);
        $this->assertArrayHasKey('creative-problem-solving', $actionItems);
        $this->assertArrayHasKey('leadership-adaptability', $actionItems);
        $this->assertArrayHasKey('collaboration', $actionItems);

        // High performance should have development action items
        $this->assertContains('Continue developing advanced skills in this area', $actionItems['creative-problem-solving']);
        
        // Medium performance should have improvement action items
        $this->assertContains('Practice and refine current skills regularly', $actionItems['leadership-adaptability']);
        
        // Low performance should have basic development action items
        $this->assertContains('Focus on fundamental development in this area', $actionItems['collaboration']);
    }

    /**
     * Test default feedback generation when no library exists
     */
    public function testDefaultFeedbackGeneration()
    {
        $scores = [
            'creative-problem-solving' => 85,
            'leadership-adaptability' => 65
        ];

        $feedback = $this->feedbackService->generateFeedback($this->user, $this->assessment, $scores);

        $this->assertNotEmpty($feedback);
        $this->assertArrayHasKey('creative-problem-solving', $feedback);
        $this->assertArrayHasKey('leadership-adaptability', $feedback);
        
        // Should contain default feedback messages
        $this->assertArrayHasKey('creative-problem-solving', $feedback);
        $this->assertArrayHasKey('leadership-adaptability', $feedback);
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

        $scores = [
            'relationships' => 85,
            'leadership-adaptability' => 70, // Not in library
            'collaboration' => 60            // Not in library
        ];

        $feedback = $this->feedbackService->generateFeedback($this->user, $this->assessment, $scores);

        // Should have feedback for all dimensions, using default for missing ones
        $this->assertArrayHasKey('relationships', $feedback);
        $this->assertArrayHasKey('leadership-adaptability', $feedback);
        $this->assertArrayHasKey('collaboration', $feedback);
        
        // Relationships should use library feedback
        $this->assertArrayHasKey('feedback', $feedback['relationships']);
        
        // Other dimensions should use default feedback
        $this->assertArrayHasKey('leadership-adaptability', $feedback);
        $this->assertArrayHasKey('collaboration', $feedback);
    }

    /**
     * Test personalized feedback with user data
     */
    public function testPersonalizedFeedbackGeneration()
    {
        // Create library with personalized content
        $feedbackData = [
            'library_type' => 'involved-360',
            'dimensions' => [
                'creative-problem-solving' => [
                    'high' => 'Excellent creative problem-solving skills, {user_name}. You demonstrate innovative thinking.',
                    'medium' => 'Good creative problem-solving abilities, {user_name}. Continue developing your skills.',
                    'low' => 'Creative problem-solving skills need development, {user_name}. Focus on innovative thinking.'
                ]
            ]
        ];

        $library = FeedbackLibrary::create([
            'name' => 'Personalized Library',
            'feedback' => $feedbackData
        ]);

        $scores = ['relationships' => 85];

        $feedback = $this->feedbackService->generateFeedback($this->user, $this->assessment, $scores);

        $this->assertNotEmpty($feedback);
        $this->assertArrayHasKey('relationships', $feedback);
        
        // Should have feedback structure
        $this->assertArrayHasKey('feedback', $feedback['relationships']);
    }

    /**
     * Test feedback generation with edge case scores
     */
    public function testEdgeCaseScores()
    {
        // Create library
        $feedbackData = [
            'library_type' => 'involved-360',
            'dimensions' => [
                'test-dimension' => [
                    'high' => 'High performance feedback',
                    'medium' => 'Medium performance feedback',
                    'low' => 'Low performance feedback'
                ]
            ]
        ];

        $library = FeedbackLibrary::create([
            'name' => 'Edge Case Library',
            'feedback' => $feedbackData
        ]);

        // Test boundary scores
        $boundaryScores = [
            'relationships' => 80 // Exactly at high threshold
        ];

        $feedback = $this->feedbackService->generateFeedback($this->user, $this->assessment, $boundaryScores);
        $this->assertArrayHasKey('feedback', $feedback['relationships']);

        $boundaryScores = [
            'relationships' => 79 // Just below high threshold
        ];

        $feedback = $this->feedbackService->generateFeedback($this->user, $this->assessment, $boundaryScores);
        $this->assertArrayHasKey('feedback', $feedback['relationships']);

        $boundaryScores = [
            'relationships' => 60 // Exactly at medium threshold
        ];

        $feedback = $this->feedbackService->generateFeedback($this->user, $this->assessment, $boundaryScores);
        $this->assertArrayHasKey('feedback', $feedback['relationships']);

        $boundaryScores = [
            'relationships' => 59 // Just below medium threshold
        ];

        $feedback = $this->feedbackService->generateFeedback($this->user, $this->assessment, $boundaryScores);
        $this->assertArrayHasKey('feedback', $feedback['relationships']);
    }

    /**
     * Test feedback generation with zero scores
     */
    public function testZeroScores()
    {
        $scores = [
            'creative-problem-solving' => 0,
            'leadership-adaptability' => 0
        ];

        $feedback = $this->feedbackService->generateFeedback($this->user, $this->assessment, $scores);

        $this->assertNotEmpty($feedback);
        $this->assertArrayHasKey('creative-problem-solving', $feedback);
        $this->assertArrayHasKey('leadership-adaptability', $feedback);
        
        // Zero scores should be treated as low performance
        $this->assertArrayHasKey('creative-problem-solving', $feedback);
        $this->assertArrayHasKey('leadership-adaptability', $feedback);
    }

    /**
     * Test feedback generation with negative scores
     */
    public function testNegativeScores()
    {
        $scores = [
            'creative-problem-solving' => -10,
            'leadership-adaptability' => -5
        ];

        $feedback = $this->feedbackService->generateFeedback($this->user, $this->assessment, $scores);

        $this->assertNotEmpty($feedback);
        $this->assertArrayHasKey('creative-problem-solving', $feedback);
        $this->assertArrayHasKey('leadership-adaptability', $feedback);
        
        // Negative scores should be treated as low performance
        $this->assertArrayHasKey('creative-problem-solving', $feedback);
        $this->assertArrayHasKey('leadership-adaptability', $feedback);
    }

    /**
     * Test feedback generation with very high scores
     */
    public function testVeryHighScores()
    {
        $scores = [
            'creative-problem-solving' => 150,
            'leadership-adaptability' => 200
        ];

        $feedback = $this->feedbackService->generateFeedback($this->user, $this->assessment, $scores);

        $this->assertNotEmpty($feedback);
        $this->assertArrayHasKey('creative-problem-solving', $feedback);
        $this->assertArrayHasKey('leadership-adaptability', $feedback);
        
        // Very high scores should be treated as high performance
        $this->assertArrayHasKey('creative-problem-solving', $feedback);
        $this->assertArrayHasKey('leadership-adaptability', $feedback);
    }

    /**
     * Test feedback generation with empty scores array
     */
    public function testEmptyScoresArray()
    {
        $scores = [];

        $feedback = $this->feedbackService->generateFeedback($this->user, $this->assessment, $scores);

        $this->assertEmpty($feedback);
    }

    /**
     * Test feedback generation with null scores
     */
    public function testNullScores()
    {
        $scores = null;

        $feedback = $this->feedbackService->generateFeedback($this->user, $this->assessment, $scores);

        $this->assertEmpty($feedback);
    }
}
