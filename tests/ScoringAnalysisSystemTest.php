<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Assignment;
use App\Assessment;
use App\Question;
use App\Answer;
use App\User;
use App\Client;
use App\Language;
use App\Job;
use App\Dimension;
use App\Weight;
use App\Benchmark;
use App\Industry;
use App\Http\Controllers\ScoringController;
use Bican\Roles\Models\Role;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ScoringAnalysisSystemTest extends TestCase
{

    protected $user;
    protected $client;
    protected $language;
    protected $assessment;
    protected $job;
    protected $assignment;
    protected $scoringController;
    protected $dimensions;
    protected $industry;

    public function setUp()
    {
        parent::setUp();
        
        // Disable CSRF protection for tests
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);
        
        // Initialize scoring controller
        $this->scoringController = new ScoringController();
        
        // Create test data
        $this->language = Language::firstOrCreate([
            'name' => 'English',
            'native_name' => 'English',
            'code' => 'en'
        ]);
        
        $this->client = Client::firstOrCreate([
            'name' => 'Test Client',
            'require_profile' => true,
            'require_research' => true
        ]);

        $this->job = Job::firstOrCreate([
            'name' => 'Test Job Position',
            'slug' => 'test-job-position-' . uniqid(),
            'client_id' => $this->client->id
        ]);

        $this->industry = Industry::firstOrCreate([
            'name' => 'Technology'
        ]);
        
        // Ensure roles exist for testing
        $this->createRolesIfNeeded();
        
        // Create test user with admin role
        $this->user = User::create([
            'username' => 'testadmin_' . uniqid(),
            'name' => 'Test Admin',
            'email' => 'admin_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'client_id' => $this->client->id,
            'language_id' => $this->language->id,
            'completed_profile' => true,
            'completed_research' => true
        ]);
        
        $adminRole = Role::where('slug', 'admin')->first();
        $this->user->attachRole($adminRole);

        // Create test assessment with dimensions and questions
        $this->createTestAssessmentWithDimensions();
        
        // Create test assignment with answers
        $this->createTestAssignmentWithAnswers();
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
     * Create test assessment with dimensions and questions
     */
    private function createTestAssessmentWithDimensions()
    {
        // Create assessment
        $assessmentData = [
            'name' => 'Test Assessment for Scoring',
            'description' => 'Assessment for testing scoring functionality',
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

        // Create dimensions
        $this->dimensions = [];
        
        // Parent dimension: Leadership
        $leadershipDimension = Dimension::create([
            'name' => 'Leadership',
            'code' => 'LEAD',
            'parent' => 0,
            'assessment_id' => $this->assessment->id
        ]);
        $this->dimensions['leadership'] = $leadershipDimension;

        // Child dimensions under Leadership
        $visionDimension = Dimension::create([
            'name' => 'Vision',
            'code' => 'VIS',
            'parent' => $leadershipDimension->id,
            'assessment_id' => $this->assessment->id
        ]);
        $this->dimensions['vision'] = $visionDimension;

        $communicationDimension = Dimension::create([
            'name' => 'Communication',
            'code' => 'COM',
            'parent' => $leadershipDimension->id,
            'assessment_id' => $this->assessment->id
        ]);
        $this->dimensions['communication'] = $communicationDimension;

        // Independent dimension: Technical Skills
        $technicalDimension = Dimension::create([
            'name' => 'Technical Skills',
            'code' => 'TECH',
            'parent' => 0,
            'assessment_id' => $this->assessment->id
        ]);
        $this->dimensions['technical'] = $technicalDimension;

        // Create questions for each dimension
        $this->createQuestionsForDimensions();
    }

    /**
     * Create questions for each dimension
     */
    private function createQuestionsForDimensions()
    {
        $questions = [
            // Vision questions
            [
                'content' => 'How well do you communicate your vision to others?',
                'number' => 1,
                'type' => 1, // Likert scale
                'dimension_id' => $this->dimensions['vision']->id,
                'anchors' => [
                    1 => ['tag' => 'Very Poor', 'value' => 1],
                    2 => ['tag' => 'Poor', 'value' => 2],
                    3 => ['tag' => 'Average', 'value' => 3],
                    4 => ['tag' => 'Good', 'value' => 4],
                    5 => ['tag' => 'Excellent', 'value' => 5]
                ]
            ],
            [
                'content' => 'How effectively do you inspire others with your vision?',
                'number' => 2,
                'type' => 1,
                'dimension_id' => $this->dimensions['vision']->id,
                'anchors' => [
                    1 => ['tag' => 'Very Poor', 'value' => 1],
                    2 => ['tag' => 'Poor', 'value' => 2],
                    3 => ['tag' => 'Average', 'value' => 3],
                    4 => ['tag' => 'Good', 'value' => 4],
                    5 => ['tag' => 'Excellent', 'value' => 5]
                ]
            ],
            // Communication questions
            [
                'content' => 'How clearly do you communicate complex ideas?',
                'number' => 3,
                'type' => 1,
                'dimension_id' => $this->dimensions['communication']->id,
                'anchors' => [
                    1 => ['tag' => 'Very Poor', 'value' => 1],
                    2 => ['tag' => 'Poor', 'value' => 2],
                    3 => ['tag' => 'Average', 'value' => 3],
                    4 => ['tag' => 'Good', 'value' => 4],
                    5 => ['tag' => 'Excellent', 'value' => 5]
                ]
            ],
            [
                'content' => 'How well do you listen to feedback?',
                'number' => 4,
                'type' => 1,
                'dimension_id' => $this->dimensions['communication']->id,
                'anchors' => [
                    1 => ['tag' => 'Very Poor', 'value' => 1],
                    2 => ['tag' => 'Poor', 'value' => 2],
                    3 => ['tag' => 'Average', 'value' => 3],
                    4 => ['tag' => 'Good', 'value' => 4],
                    5 => ['tag' => 'Excellent', 'value' => 5]
                ]
            ],
            // Technical Skills questions
            [
                'content' => 'How proficient are you with relevant technical tools?',
                'number' => 5,
                'type' => 1,
                'dimension_id' => $this->dimensions['technical']->id,
                'anchors' => [
                    1 => ['tag' => 'Beginner', 'value' => 1],
                    2 => ['tag' => 'Basic', 'value' => 2],
                    3 => ['tag' => 'Intermediate', 'value' => 3],
                    4 => ['tag' => 'Advanced', 'value' => 4],
                    5 => ['tag' => 'Expert', 'value' => 5]
                ]
            ],
            [
                'content' => 'How quickly do you learn new technologies?',
                'number' => 6,
                'type' => 1,
                'dimension_id' => $this->dimensions['technical']->id,
                'anchors' => [
                    1 => ['tag' => 'Very Slow', 'value' => 1],
                    2 => ['tag' => 'Slow', 'value' => 2],
                    3 => ['tag' => 'Average', 'value' => 3],
                    4 => ['tag' => 'Fast', 'value' => 4],
                    5 => ['tag' => 'Very Fast', 'value' => 5]
                ]
            ]
        ];

        foreach ($questions as $questionData) {
            $question = new Question([
                'content' => $questionData['content'],
                'number' => $questionData['number'],
                'type' => $questionData['type'],
                'dimension_id' => $questionData['dimension_id'],
                'anchors' => $questionData['anchors']
            ]);
            $this->assessment->questions()->save($question);
        }
    }

    /**
     * Create test assignment with answers
     */
    private function createTestAssignmentWithAnswers()
    {
        // Create assignment
        $assignmentData = [
            'user_id' => $this->user->id,
            'assessment_id' => $this->assessment->id,
            'job_id' => $this->job->id,
            'completed' => true,
            'completed_at' => Carbon::now(),
            'started_at' => Carbon::now()->subMinutes(30)
        ];

        $this->assignment = new Assignment($assignmentData);
        $this->user->assignments()->save($this->assignment);

        // Create answers for all questions
        $questions = $this->assessment->questions()->orderBy('number')->get();
        $answerValues = [4, 5, 3, 4, 3, 4]; // Predefined scores for consistent testing

        foreach ($questions as $index => $question) {
            $answerData = [
                'assignment_id' => $this->assignment->id,
                'question_id' => $question->id,
                'user_id' => $this->user->id,
                'value' => $answerValues[$index],
                'time' => 20
            ];

            $answer = new Answer($answerData);
            $this->assignment->answers()->save($answer);
        }
    }

    // ========================================
    // RAW SCORE CALCULATION TESTS
    // ========================================

    /**
     * Test basic total score calculation
     */
    public function testGetTotalScore()
    {
        $totalScore = $this->scoringController->getTotalScore($this->assignment);
        
        // Expected: 4 + 5 + 3 + 4 + 3 + 4 = 23
        $this->assertEquals(23, $totalScore);
    }

    /**
     * Test score calculation with no answers
     */
    public function testGetTotalScoreWithNoAnswers()
    {
        // Create assignment without answers
        $emptyAssignmentData = [
            'user_id' => $this->user->id,
            'assessment_id' => $this->assessment->id,
            'job_id' => $this->job->id,
            'completed' => false
        ];

        $emptyAssignment = new Assignment($emptyAssignmentData);
        $this->user->assignments()->save($emptyAssignment);

        $totalScore = $this->scoringController->getTotalScore($emptyAssignment);
        $this->assertEquals(0, $totalScore);
    }

    /**
     * Test score average calculation
     */
    public function testGetScoreAverage()
    {
        $answers = $this->assignment->answers()->get();
        $averageScore = $this->scoringController->getScoreAverage($answers);
        
        // Expected: (4 + 5 + 3 + 4 + 3 + 4) / 6 = 23/6 ≈ 3.83
        $this->assertEquals(23/6, $averageScore, '', 0.01);
    }

    // ========================================
    // DIMENSION SCORE CALCULATION TESTS
    // ========================================

    /**
     * Test dimension score calculation for child dimension
     */
    public function testGetScoreForChildDimension()
    {
        $visionScore = $this->scoringController->getScoreForDimension(
            $this->assignment->id, 
            $this->dimensions['vision']->id
        );
        
        // Expected: (4 + 5) / 2 = 4.5 (Vision dimension has 2 questions with scores 4 and 5)
        $this->assertEquals(4.5, $visionScore);
    }

    /**
     * Test dimension score calculation for parent dimension
     */
    public function testGetScoreForParentDimension()
    {
        $leadershipScore = $this->scoringController->getScoreForDimension(
            $this->assignment->id, 
            $this->dimensions['leadership']->id
        );
        
        // Expected: Average of child dimensions
        // Vision: (4 + 5) / 2 = 4.5
        // Communication: (3 + 4) / 2 = 3.5
        // Leadership: (4.5 + 3.5) / 2 = 4.0
        $this->assertEquals(4.0, $leadershipScore);
    }

    /**
     * Test dimension score average calculation
     */
    public function testGetScoreAverageForDimension()
    {
        $technicalScore = $this->scoringController->getScoreAverageForDimension(
            $this->assignment, 
            $this->dimensions['technical']
        );
        
        // Expected: (3 + 4) / 2 = 3.5 (Technical dimension has 2 questions with scores 3 and 4)
        $this->assertEquals(3.5, $technicalScore);
    }

    // ========================================
    // WEIGHTED SCORE CALCULATION TESTS
    // ========================================

    /**
     * Test weighted score calculation with custom weights
     */
    public function testGetWeightedScoreAverageWithCustomWeights()
    {
        // Authenticate the user for the test
        $this->be($this->user);
        
        // Create custom weights for the job
        $customWeights = [
            $this->dimensions['leadership']->id => 60, // 60% weight
            $this->dimensions['technical']->id => 40   // 40% weight
        ];

        $weightData = [
            'assessment_id' => $this->assessment->id,
            'weights' => $customWeights,
            'divisions' => []
        ];
        
        $weight = new Weight($weightData);
        $this->job->weights()->save($weight);

        $weightedScore = $this->scoringController->getWeightedScoreAverage($this->assignment, $this->job);
        
        // Expected calculation:
        // Leadership score: 4.0 * 0.60 = 2.4
        // Technical score: 3.5 * 0.40 = 1.4
        // Total: 2.4 + 1.4 = 3.8
        $this->assertEquals(3.8, $weightedScore, '', 0.01);
    }

    /**
     * Test weighted score calculation with equal weights (no custom weights)
     */
    public function testGetWeightedScoreAverageWithEqualWeights()
    {
        // Authenticate the user for the test
        $this->be($this->user);
        
        $weightedScore = $this->scoringController->getWeightedScoreAverage($this->assignment, $this->job);
        
        // Expected: Equal weighting between Leadership (4.0) and Technical (3.5)
        // (4.0 * 0.5) + (3.5 * 0.5) = 2.0 + 1.75 = 3.75
        $this->assertEquals(3.75, $weightedScore, '', 0.01);
    }

    // ========================================
    // BENCHMARK COMPARISON TESTS
    // ========================================

    /**
     * Test benchmark creation and comparison
     */
    public function testBenchmarkCreationAndComparison()
    {
        // Create benchmarks for each dimension
        $leadershipBenchmark = Benchmark::create([
            'dimension_id' => $this->dimensions['leadership']->id,
            'industry_id' => $this->industry->id,
            'value' => 3.5
        ]);

        $technicalBenchmark = Benchmark::create([
            'dimension_id' => $this->dimensions['technical']->id,
            'industry_id' => $this->industry->id,
            'value' => 3.0
        ]);

        $this->assertNotNull($leadershipBenchmark->id);
        $this->assertNotNull($technicalBenchmark->id);
        $this->assertEquals(3.5, $leadershipBenchmark->value);
        $this->assertEquals(3.0, $technicalBenchmark->value);

        // Test benchmark relationships
        $this->assertEquals($this->dimensions['leadership']->id, $leadershipBenchmark->dimension_id);
        $this->assertEquals($this->industry->id, $leadershipBenchmark->industry_id);
    }

    /**
     * Test benchmark scopes
     */
    public function testBenchmarkScopes()
    {
        // Create benchmarks
        $leadershipBenchmark = Benchmark::create([
            'dimension_id' => $this->dimensions['leadership']->id,
            'industry_id' => $this->industry->id,
            'value' => 3.5
        ]);

        $technicalBenchmark = Benchmark::create([
            'dimension_id' => $this->dimensions['technical']->id,
            'industry_id' => $this->industry->id,
            'value' => 3.0
        ]);

        // Test forIndustry scope
        $industryBenchmarks = Benchmark::forIndustry($this->industry->id)->get();
        $this->assertGreaterThanOrEqual(2, $industryBenchmarks->count());

        // Test forDimension scope
        $leadershipBenchmarks = Benchmark::forDimension($this->dimensions['leadership']->id)->get();
        $this->assertCount(1, $leadershipBenchmarks);
        $this->assertEquals($leadershipBenchmark->id, $leadershipBenchmarks->first()->id);

        // Test forAssessment scope
        $assessmentBenchmarks = Benchmark::forAssessment($this->assessment->id)->get();
        $this->assertCount(2, $assessmentBenchmarks);
    }

    // ========================================
    // SCORE PERSISTENCE TESTS
    // ========================================

    /**
     * Test score persistence in report_data table
     */
    public function testScorePersistence()
    {
        // Clear any existing report data
        DB::table('report_data')->where('assignment_id', $this->assignment->id)->delete();

        // Calculate score (should save to database)
        $score = $this->scoringController->score($this->assignment->id, $this->job->id);

        // Verify score was saved
        $savedScore = DB::table('report_data')
            ->where('assignment_id', $this->assignment->id)
            ->first();

        $this->assertNotNull($savedScore);
        $this->assertEquals($this->assignment->user_id, $savedScore->user_id);
        $this->assertEquals($this->assignment->id, $savedScore->assignment_id);
        $this->assertEquals(json_encode($score), $savedScore->score);
    }

    /**
     * Test score retrieval from cache
     */
    public function testScoreRetrievalFromCache()
    {
        // Clear any existing report data
        DB::table('report_data')->where('assignment_id', $this->assignment->id)->delete();

        // First call - should calculate and save
        $firstScore = $this->scoringController->score($this->assignment->id, $this->job->id);

        // Second call - should retrieve from cache
        $secondScore = $this->scoringController->score($this->assignment->id, $this->job->id);

        $this->assertEquals($firstScore, $secondScore);

        // Verify only one record exists in database
        $recordCount = DB::table('report_data')
            ->where('assignment_id', $this->assignment->id)
            ->count();
        $this->assertEquals(1, $recordCount);
    }

    // ========================================
    // MISSING DATA HANDLING TESTS
    // ========================================

    /**
     * Test score calculation with missing answers
     */
    public function testScoreCalculationWithMissingAnswers()
    {
        // Create assignment with only partial answers
        $partialAssignmentData = [
            'user_id' => $this->user->id,
            'assessment_id' => $this->assessment->id,
            'job_id' => $this->job->id,
            'completed' => false
        ];

        $partialAssignment = new Assignment($partialAssignmentData);
        $this->user->assignments()->save($partialAssignment);

        // Add only 2 answers out of 6 questions
        $questions = $this->assessment->questions()->take(2)->get();
        foreach ($questions as $question) {
            $answerData = [
                'assignment_id' => $partialAssignment->id,
                'question_id' => $question->id,
                'user_id' => $this->user->id,
                'value' => 3,
                'time' => 20
            ];

            $answer = new Answer($answerData);
            $partialAssignment->answers()->save($answer);
        }

        // Test total score calculation
        $totalScore = $this->scoringController->getTotalScore($partialAssignment);
        $this->assertEquals(6, $totalScore); // 2 answers * 3 points each

        // Test dimension score calculation (should handle missing data gracefully)
        $visionScore = $this->scoringController->getScoreAverageForDimension(
            $partialAssignment, 
            $this->dimensions['vision']
        );
        $this->assertEquals(3.0, $visionScore); // Only one answer for vision dimension
    }

    // ========================================
    // SCORE VALIDATION TESTS
    // ========================================

    /**
     * Test score validation and bounds checking
     */
    public function testScoreValidationAndBounds()
    {
        // Test with extreme values
        $extremeAssignmentData = [
            'user_id' => $this->user->id,
            'assessment_id' => $this->assessment->id,
            'job_id' => $this->job->id,
            'completed' => true
        ];

        $extremeAssignment = new Assignment($extremeAssignmentData);
        $this->user->assignments()->save($extremeAssignment);

        // Create answers with maximum values
        $questions = $this->assessment->questions()->get();
        foreach ($questions as $question) {
            $answerData = [
                'assignment_id' => $extremeAssignment->id,
                'question_id' => $question->id,
                'user_id' => $this->user->id,
                'value' => 5, // Maximum value
                'time' => 20
            ];

            $answer = new Answer($answerData);
            $extremeAssignment->answers()->save($answer);
        }

        $totalScore = $this->scoringController->getTotalScore($extremeAssignment);
        $this->assertEquals(30, $totalScore); // 6 questions * 5 points each

        $averageScore = $this->scoringController->getScoreAverage($extremeAssignment->answers()->get());
        $this->assertEquals(5.0, $averageScore);
    }

    /**
     * Test score calculation accuracy with decimal precision
     */
    public function testScoreCalculationPrecision()
    {
        // Create assignment with specific values for precision testing
        $precisionAssignmentData = [
            'user_id' => $this->user->id,
            'assessment_id' => $this->assessment->id,
            'job_id' => $this->job->id,
            'completed' => true
        ];

        $precisionAssignment = new Assignment($precisionAssignmentData);
        $this->user->assignments()->save($precisionAssignment);

        // Create answers with values that will test decimal precision
        $questions = $this->assessment->questions()->get();
        $precisionValues = [1, 2, 3, 4, 5, 1]; // Average should be 2.666...

        foreach ($questions as $index => $question) {
            $answerData = [
                'assignment_id' => $precisionAssignment->id,
                'question_id' => $question->id,
                'user_id' => $this->user->id,
                'value' => $precisionValues[$index],
                'time' => 20
            ];

            $answer = new Answer($answerData);
            $precisionAssignment->answers()->save($answer);
        }

        $averageScore = $this->scoringController->getScoreAverage($precisionAssignment->answers()->get());
        $this->assertEquals(16/6, $averageScore, '', 0.001); // 2.666... with precision
    }
}
