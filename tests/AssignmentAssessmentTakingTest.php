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
use Spatie\Permission\Models\Role;
use Carbon\Carbon;

class AssignmentAssessmentTakingTest extends TestCase
{

    protected $user;
    protected $client;
    protected $language;
    protected $assessment;
    protected $job;

    public function setUp(): void
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
        
        $this->client = Client::firstOrCreate([
            'name' => 'Test Client',
            'require_profile' => true,
            'require_research' => true
        ]);

        $this->job = Job::firstOrCreate([
            'name' => 'Test Job Position',
            'client_id' => $this->client->id
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

        // Create test assessment with questions
        $assessmentData = [
            'name' => 'Test Assessment',
            'description' => 'Test assessment for assignment testing',
            'use_custom_fields' => false,
            'whitelabel' => false,
            'translation' => false,
            'timed' => true,
            'time_limit' => 30, // 30 minutes
            'paginate' => false,
            'custom_fields' => [],
            'anchors' => []
        ];
        
        $this->assessment = new Assessment($assessmentData);
        $this->user->assessments()->save($this->assessment);

        // Create test questions
        $this->createTestQuestions();
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

    /**
     * Create test questions for the assessment
     */
    private function createTestQuestions()
    {
        $questions = [
            [
                'content' => 'How satisfied are you with your current role?',
                'number' => 1,
                'type' => 1, // Likert scale
                'anchors' => [
                    1 => ['tag' => 'Very Dissatisfied', 'value' => 1],
                    2 => ['tag' => 'Dissatisfied', 'value' => 2],
                    3 => ['tag' => 'Neutral', 'value' => 3],
                    4 => ['tag' => 'Satisfied', 'value' => 4],
                    5 => ['tag' => 'Very Satisfied', 'value' => 5]
                ]
            ],
            [
                'content' => 'How likely are you to recommend this company?',
                'number' => 2,
                'type' => 1, // Likert scale
                'anchors' => [
                    1 => ['tag' => 'Very Unlikely', 'value' => 1],
                    2 => ['tag' => 'Unlikely', 'value' => 2],
                    3 => ['tag' => 'Neutral', 'value' => 3],
                    4 => ['tag' => 'Likely', 'value' => 4],
                    5 => ['tag' => 'Very Likely', 'value' => 5]
                ]
            ],
            [
                'content' => 'Please describe your ideal work environment.',
                'number' => 3,
                'type' => 2, // Text question
                'anchors' => []
            ]
        ];

        foreach ($questions as $questionData) {
            $question = new Question([
                'content' => $questionData['content'],
                'number' => $questionData['number'],
                'type' => $questionData['type'],
                'anchors' => $questionData['anchors']
            ]);
            $this->assessment->questions()->save($question);
        }
    }

    // ========================================
    // ASSIGNMENT CREATION TESTS
    // ========================================

    /**
     * Test assignment creation with basic data
     */
    public function testAssignmentCreation()
    {
        $assignmentData = [
            'user_id' => $this->user->id,
            'assessment_id' => $this->assessment->id,
            'job_id' => $this->job->id,
            'completed' => false,
            'custom_fields' => ['field1' => 'value1'],
            'whitelabel' => false
        ];

        $assignment = new Assignment($assignmentData);
        $this->user->assignments()->save($assignment);

        $this->assertNotNull($assignment->id);
        $this->assertEquals($this->user->id, $assignment->user_id);
        $this->assertEquals($this->assessment->id, $assignment->assessment_id);
        $this->assertEquals($this->job->id, $assignment->job_id);
        $this->assertFalse((bool)$assignment->completed);
        $this->assertTrue(is_array($assignment->custom_fields));
        $this->assertEquals('value1', $assignment->custom_fields['field1']);
    }

    /**
     * Test assignment creation with scheduling
     */
    public function testAssignmentScheduling()
    {
        $futureDate = Carbon::now()->addDays(7);
        
        $assignmentData = [
            'user_id' => $this->user->id,
            'assessment_id' => $this->assessment->id,
            'job_id' => $this->job->id,
            'started_at' => $futureDate,
            'expires' => $futureDate->copy()->addDays(30),
            'completed' => false
        ];

        $assignment = new Assignment($assignmentData);
        $this->user->assignments()->save($assignment);

        $this->assertNotNull($assignment->id);
        $this->assertEquals($futureDate->format('Y-m-d H:i:s'), $assignment->started_at->format('Y-m-d H:i:s'));
        $this->assertNotNull($assignment->expires);
    }

    /**
     * Test bulk assignment creation
     */
    public function testBulkAssignmentCreation()
    {
        // Create additional test users
        $users = [];
        for ($i = 0; $i < 3; $i++) {
            $user = User::create([
                'username' => 'testuser_' . uniqid() . '_' . $i,
                'name' => 'Test User ' . $i,
                'email' => 'user_' . uniqid() . '_' . $i . '@example.com',
                'password' => bcrypt('password'),
                'client_id' => $this->client->id,
                'language_id' => $this->language->id,
                'completed_profile' => true,
                'completed_research' => true
            ]);
            $users[] = $user;
        }

        $assignments = [];
        foreach ($users as $user) {
            $assignmentData = [
                'user_id' => $user->id,
                'assessment_id' => $this->assessment->id,
                'job_id' => $this->job->id,
                'completed' => false
            ];

            $assignment = new Assignment($assignmentData);
            $user->assignments()->save($assignment);
            $assignments[] = $assignment;
        }

        $this->assertCount(3, $assignments);
        foreach ($assignments as $assignment) {
            $this->assertNotNull($assignment->id);
            $this->assertFalse((bool)$assignment->completed);
        }
    }

    // ========================================
    // ASSESSMENT TAKING PROCESS TESTS
    // ========================================

    /**
     * Test assessment start and initialization
     */
    public function testAssessmentStartAndInitialization()
    {
        $assignmentData = [
            'user_id' => $this->user->id,
            'assessment_id' => $this->assessment->id,
            'job_id' => $this->job->id,
            'completed' => false
        ];

        $assignment = new Assignment($assignmentData);
        $this->user->assignments()->save($assignment);

        // Simulate starting the assessment
        $assignment->started_at = Carbon::now();
        $assignment->save();

        $this->assertNotNull($assignment->started_at);
        $this->assertNull($assignment->completed_at);
        $this->assertFalse((bool)$assignment->completed);
    }

    /**
     * Test question presentation and navigation
     */
    public function testQuestionPresentationAndNavigation()
    {
        $assignmentData = [
            'user_id' => $this->user->id,
            'assessment_id' => $this->assessment->id,
            'job_id' => $this->job->id,
            'completed' => false
        ];

        $assignment = new Assignment($assignmentData);
        $this->user->assignments()->save($assignment);

        // Get questions for the assessment
        $questions = $this->assessment->questions()->orderBy('number')->get();
        
        $this->assertCount(3, $questions);
        $this->assertEquals(1, $questions->first()->number);
        $this->assertEquals(3, $questions->last()->number);
        
        // Test question content and type
        $firstQuestion = $questions->first();
        $this->assertEquals('How satisfied are you with your current role?', $firstQuestion->content);
        $this->assertEquals(1, $firstQuestion->type); // Likert scale
        $this->assertTrue(is_array($firstQuestion->anchors));
    }

    /**
     * Test answer capture and validation
     */
    public function testAnswerCaptureAndValidation()
    {
        $assignmentData = [
            'user_id' => $this->user->id,
            'assessment_id' => $this->assessment->id,
            'job_id' => $this->job->id,
            'completed' => false
        ];

        $assignment = new Assignment($assignmentData);
        $this->user->assignments()->save($assignment);

        $questions = $this->assessment->questions()->orderBy('number')->get();
        $firstQuestion = $questions->first();

        // Create answer for Likert scale question
        $answerData = [
            'assignment_id' => $assignment->id,
            'question_id' => $firstQuestion->id,
            'user_id' => $this->user->id,
            'value' => 4, // Satisfied
            'time' => 15 // 15 seconds
        ];

        $answer = new Answer($answerData);
        $assignment->answers()->save($answer);

        $this->assertNotNull($answer->id);
        $this->assertEquals($assignment->id, $answer->assignment_id);
        $this->assertEquals($firstQuestion->id, $answer->question_id);
        $this->assertEquals($this->user->id, $answer->user_id);
        $this->assertEquals(4, $answer->value);
        $this->assertEquals(15, $answer->time);

        // Test answer scoring
        $this->assertEquals(4, $answer->questionScore());
        $this->assertEquals('Satisfied', $answer->questionText());
    }

    /**
     * Test text answer capture
     */
    public function testTextAnswerCapture()
    {
        $assignmentData = [
            'user_id' => $this->user->id,
            'assessment_id' => $this->assessment->id,
            'job_id' => $this->job->id,
            'completed' => false
        ];

        $assignment = new Assignment($assignmentData);
        $this->user->assignments()->save($assignment);

        $textQuestion = $this->assessment->questions()->where('type', 2)->first();

        // Create text answer
        $answerData = [
            'assignment_id' => $assignment->id,
            'question_id' => $textQuestion->id,
            'user_id' => $this->user->id,
            'value' => 'I prefer a collaborative environment with flexible hours.',
            'time' => 45
        ];

        $answer = new Answer($answerData);
        $assignment->answers()->save($answer);

        $this->assertNotNull($answer->id);
        $this->assertEquals('I prefer a collaborative environment with flexible hours.', $answer->value);
        $this->assertEquals(45, $answer->time);
    }

    /**
     * Test time limit enforcement
     */
    public function testTimeLimitEnforcement()
    {
        // Create assessment with short time limit for testing
        $timedAssessmentData = [
            'name' => 'Timed Test Assessment',
            'description' => 'Assessment with time limit',
            'use_custom_fields' => false,
            'whitelabel' => false,
            'translation' => false,
            'timed' => true,
            'time_limit' => 1, // 1 minute for testing
            'paginate' => false,
            'custom_fields' => [],
            'anchors' => []
        ];
        
        $timedAssessment = new Assessment($timedAssessmentData);
        $this->user->assessments()->save($timedAssessment);

        $assignmentData = [
            'user_id' => $this->user->id,
            'assessment_id' => $timedAssessment->id,
            'job_id' => $this->job->id,
            'completed' => false,
            'started_at' => Carbon::now()->subMinutes(2) // Started 2 minutes ago
        ];

        $assignment = new Assignment($assignmentData);
        $this->user->assignments()->save($assignment);

        // Check if assignment is expired
        $isExpired = $assignment->started_at->addMinutes($timedAssessment->time_limit)->isPast();
        $this->assertTrue($isExpired);
    }

    /**
     * Test progress saving and recovery
     */
    public function testProgressSavingAndRecovery()
    {
        $assignmentData = [
            'user_id' => $this->user->id,
            'assessment_id' => $this->assessment->id,
            'job_id' => $this->job->id,
            'completed' => false
        ];

        $assignment = new Assignment($assignmentData);
        $this->user->assignments()->save($assignment);

        $questions = $this->assessment->questions()->orderBy('number')->get();

        // Save partial progress (first 2 questions)
        for ($i = 0; $i < 2; $i++) {
            $question = $questions[$i];
            $answerData = [
                'assignment_id' => $assignment->id,
                'question_id' => $question->id,
                'user_id' => $this->user->id,
                'value' => $i + 3, // Different values for each question
                'time' => ($i + 1) * 10
            ];

            $answer = new Answer($answerData);
            $assignment->answers()->save($answer);
        }

        // Verify partial progress is saved
        $savedAnswers = $assignment->answers()->get();
        $this->assertCount(2, $savedAnswers);

        // Simulate recovery - get assignment and answers
        $recoveredAssignment = Assignment::find($assignment->id);
        $recoveredAnswers = $recoveredAssignment->answers()->get();
        
        $this->assertCount(2, $recoveredAnswers);
        $this->assertEquals(3, $recoveredAnswers->first()->value);
        $this->assertEquals(4, $recoveredAnswers->last()->value);
    }

    // ========================================
    // ASSIGNMENT COMPLETION TESTS
    // ========================================

    /**
     * Test assignment completion workflow
     */
    public function testAssignmentCompletionWorkflow()
    {
        $assignmentData = [
            'user_id' => $this->user->id,
            'assessment_id' => $this->assessment->id,
            'job_id' => $this->job->id,
            'completed' => false
        ];

        $assignment = new Assignment($assignmentData);
        $this->user->assignments()->save($assignment);

        // Start assessment
        $assignment->started_at = Carbon::now();
        $assignment->save();

        // Answer all questions
        $questions = $this->assessment->questions()->orderBy('number')->get();
        foreach ($questions as $question) {
            $answerData = [
                'assignment_id' => $assignment->id,
                'question_id' => $question->id,
                'user_id' => $this->user->id,
                'value' => $question->type == 1 ? 4 : 'Sample text answer',
                'time' => 20
            ];

            $answer = new Answer($answerData);
            $assignment->answers()->save($answer);
        }

        // Complete assignment
        $assignment->completed = true;
        $assignment->completed_at = Carbon::now();
        $assignment->save();

        $this->assertTrue((bool)$assignment->completed);
        $this->assertNotNull($assignment->completed_at);
        $this->assertCount(3, $assignment->answers()->get());
    }

    /**
     * Test data integrity verification
     */
    public function testDataIntegrityVerification()
    {
        $assignmentData = [
            'user_id' => $this->user->id,
            'assessment_id' => $this->assessment->id,
            'job_id' => $this->job->id,
            'completed' => false
        ];

        $assignment = new Assignment($assignmentData);
        $this->user->assignments()->save($assignment);

        // Answer all questions
        $questions = $this->assessment->questions()->orderBy('number')->get();
        $totalQuestions = $questions->count();
        
        foreach ($questions as $question) {
            $answerData = [
                'assignment_id' => $assignment->id,
                'question_id' => $question->id,
                'user_id' => $this->user->id,
                'value' => $question->type == 1 ? 4 : 'Sample text answer',
                'time' => 20
            ];

            $answer = new Answer($answerData);
            $assignment->answers()->save($answer);
        }

        // Verify data integrity
        $completedAnswers = $assignment->answers()->get();
        $this->assertCount($totalQuestions, $completedAnswers);
        
        // Verify all questions have answers
        $answeredQuestionIds = $completedAnswers->pluck('question_id')->toArray();
        $allQuestionIds = $questions->pluck('id')->toArray();
        $this->assertEquals($allQuestionIds, $answeredQuestionIds);
    }

    /**
     * Test assignment URL generation and validation
     */
    public function testAssignmentUrlGenerationAndValidation()
    {
        // Set SERVER_NAME for testing
        $_SERVER['SERVER_NAME'] = 'localhost';
        
        $assignmentData = [
            'user_id' => $this->user->id,
            'assessment_id' => $this->assessment->id,
            'job_id' => $this->job->id,
            'completed' => false
        ];

        $assignment = new Assignment($assignmentData);
        $this->user->assignments()->save($assignment);

        // Generate assignment URL
        $expires = Carbon::now()->addDays(7);
        $url = Assignment::generateURL($assignment->id, $this->user->username, $expires);

        $this->assertNotEmpty($url);
        $this->assertContains('assignment/' . $assignment->id, $url);
        $this->assertContains('u=', $url);
        $this->assertContains('e=', $url);
        $this->assertContains('t=', $url);
    }

    /**
     * Test assignment relationships
     */
    public function testAssignmentRelationships()
    {
        $assignmentData = [
            'user_id' => $this->user->id,
            'assessment_id' => $this->assessment->id,
            'job_id' => $this->job->id,
            'completed' => false
        ];

        $assignment = new Assignment($assignmentData);
        $this->user->assignments()->save($assignment);

        // Test user relationship
        $this->assertEquals($this->user->id, $assignment->user->id);
        $this->assertEquals($this->user->name, $assignment->user->name);

        // Test assessment relationship
        $this->assertEquals($this->assessment->id, $assignment->assessment()->id);
        $this->assertEquals($this->assessment->name, $assignment->assessment()->name);

        // Test job relationship
        $this->assertEquals($this->job->id, $assignment->job->id);
        $this->assertEquals($this->job->name, $assignment->job->name);

        // Test answers relationship (empty initially)
        $this->assertCount(0, $assignment->answers()->get());
    }

    /**
     * Test assignment scopes
     */
    public function testAssignmentScopes()
    {
        // Create completed assignment
        $completedAssignmentData = [
            'user_id' => $this->user->id,
            'assessment_id' => $this->assessment->id,
            'job_id' => $this->job->id,
            'completed' => true,
            'completed_at' => Carbon::now()
        ];

        $completedAssignment = new Assignment($completedAssignmentData);
        $this->user->assignments()->save($completedAssignment);

        // Test isCompleted scope
        $completedAssignments = Assignment::isCompleted()->get();
        $this->assertGreaterThan(0, $completedAssignments->count());
        $this->assertTrue($completedAssignments->contains($completedAssignment));
    }
}
