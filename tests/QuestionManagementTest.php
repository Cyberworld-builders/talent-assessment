<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Question;
use App\Assessment;
use App\Dimension;
use App\User;
use App\Client;
use App\Language;
use Spatie\Permission\Models\Role;

class QuestionManagementTest extends TestCase
{

    protected $user;
    protected $client;
    protected $language;
    protected $dimension;
    protected $assessment;

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

        $this->dimension = Dimension::firstOrCreate([
            'name' => 'Test Dimension',
            'parent' => 0,
            'code' => 'TEST'
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

        // Create test assessment
        $this->assessment = new Assessment([
            'name' => 'Test Assessment for Questions',
            'description' => 'Assessment for testing question management',
            'target' => 0
        ]);
        $this->user->assessments()->save($this->assessment);
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

    // ========================================
    // QUESTION CREATION TESTS
    // ========================================

    /**
     * Test creating a Likert-type question
     */
    public function testCreateLikertQuestion()
    {
        $this->actingAs($this->user);

        $questionData = [
            'content' => 'How do you rate your communication skills?',
            'number' => 1,
            'type' => 1, // Likert
            'dimension_id' => $this->dimension->id,
            'practice' => false
        ];

        $question = new Question($questionData);
        $this->assessment->questions()->save($question);

        // Verify question was created
        $this->assertNotNull($question->id);
        $this->assertEquals('How do you rate your communication skills?', $question->content);
        $this->assertEquals(1, $question->number);
        $this->assertEquals(1, $question->type);
        $this->assertEquals($this->dimension->id, $question->dimension_id);
        $this->assertFalse((bool)$question->practice);
        $this->assertEquals($this->assessment->id, $question->assessment_id);
    }

    /**
     * Test creating a multiple choice question with anchors
     */
    public function testCreateMultipleChoiceQuestion()
    {
        $this->actingAs($this->user);

        $anchors = [
            'Strongly Disagree' => 1,
            'Disagree' => 2,
            'Neutral' => 3,
            'Agree' => 4,
            'Strongly Agree' => 5
        ];

        $questionData = [
            'content' => 'I enjoy working in teams',
            'number' => 2,
            'type' => 2, // Multiple choice
            'dimension_id' => $this->dimension->id,
            'anchors' => $anchors,
            'practice' => false
        ];

        $question = new Question($questionData);
        $this->assessment->questions()->save($question);

        // Verify question was created
        $this->assertNotNull($question->id);
        $this->assertEquals('I enjoy working in teams', $question->content);
        $this->assertEquals(2, $question->type);
        $this->assertTrue(is_array($question->anchors));
        $this->assertEquals(5, count($question->anchors));
        $this->assertEquals('Strongly Agree', array_search(5, $question->anchors));
    }

    /**
     * Test creating a text question as practice
     */
    public function testCreatePracticeTextQuestion()
    {
        $this->actingAs($this->user);

        $questionData = [
            'content' => 'Please describe your leadership style',
            'number' => 3,
            'type' => 3, // Text
            'dimension_id' => $this->dimension->id,
            'practice' => true
        ];

        $question = new Question($questionData);
        $this->assessment->questions()->save($question);

        // Verify practice question was created
        $this->assertNotNull($question->id);
        $this->assertEquals('Please describe your leadership style', $question->content);
        $this->assertEquals(3, $question->type);
        $this->assertTrue((bool)$question->practice);
    }

    /**
     * Test creating a question without dimension
     */
    public function testCreateQuestionWithoutDimension()
    {
        $this->actingAs($this->user);

        $questionData = [
            'content' => 'General question without specific dimension',
            'number' => 4,
            'type' => 1, // Likert
            'practice' => false
        ];

        $question = new Question($questionData);
        $this->assessment->questions()->save($question);

        // Verify question was created without dimension
        $this->assertNotNull($question->id);
        $this->assertNull($question->dimension_id);
    }

    // ========================================
    // QUESTION CONFIGURATION TESTS
    // ========================================

    /**
     * Test question numbering and ordering
     */
    public function testQuestionNumberingAndOrdering()
    {
        $this->actingAs($this->user);

        // Create multiple questions with different numbers
        $questions = [
            [
                'content' => 'First question',
                'number' => 1,
                'type' => 1
            ],
            [
                'content' => 'Third question',
                'number' => 3,
                'type' => 1
            ],
            [
                'content' => 'Second question',
                'number' => 2,
                'type' => 1
            ]
        ];

        foreach ($questions as $questionData) {
            $question = new Question($questionData);
            $this->assessment->questions()->save($question);
        }

        // Verify questions are ordered by number
        $orderedQuestions = $this->assessment->questions()->orderBy('number')->get();
        $this->assertEquals(3, $orderedQuestions->count());
        $this->assertEquals('First question', $orderedQuestions[0]->content);
        $this->assertEquals('Second question', $orderedQuestions[1]->content);
        $this->assertEquals('Third question', $orderedQuestions[2]->content);
    }

    /**
     * Test question anchors configuration
     */
    public function testQuestionAnchorsConfiguration()
    {
        $this->actingAs($this->user);

        $anchors = [
            'Never' => 1,
            'Rarely' => 2,
            'Sometimes' => 3,
            'Often' => 4,
            'Always' => 5
        ];

        $questionData = [
            'content' => 'How often do you take initiative?',
            'number' => 1,
            'type' => 2, // Multiple choice
            'anchors' => $anchors
        ];

        $question = new Question($questionData);
        $this->assessment->questions()->save($question);

        // Verify anchors are properly stored and retrieved
        $this->assertTrue(is_array($question->anchors));
        $this->assertEquals(5, count($question->anchors));
        $this->assertEquals(1, $question->anchors['Never']);
        $this->assertEquals(5, $question->anchors['Always']);
    }

    /**
     * Test question validation
     */
    public function testQuestionValidation()
    {
        $this->actingAs($this->user);

        // Test that question can be created with minimal required fields
        $question = new Question([
            'content' => 'Test question',
            'number' => 1,
            'type' => 1
        ]);
        $this->assessment->questions()->save($question);

        // Verify question was created successfully
        $this->assertNotNull($question->id);
        $this->assertEquals('Test question', $question->content);
    }

    // ========================================
    // QUESTION MANAGEMENT TESTS
    // ========================================

    /**
     * Test question editing and updates
     */
    public function testQuestionEditing()
    {
        $this->actingAs($this->user);

        $question = new Question([
            'content' => 'Original question content',
            'number' => 1,
            'type' => 1,
            'dimension_id' => $this->dimension->id
        ]);
        $this->assessment->questions()->save($question);

        // Update question
        $question->update([
            'content' => 'Updated question content',
            'number' => 2
        ]);

        // Refresh from database
        $question = Question::find($question->id);

        // Verify updates
        $this->assertEquals('Updated question content', $question->content);
        $this->assertEquals(2, $question->number);
    }

    /**
     * Test question relationships
     */
    public function testQuestionRelationships()
    {
        $this->actingAs($this->user);

        $question = new Question([
            'content' => 'Test question',
            'number' => 1,
            'type' => 1,
            'dimension_id' => $this->dimension->id
        ]);
        $this->assessment->questions()->save($question);

        // Test assessment relationship
        $this->assertEquals($this->assessment->id, $question->assessment->id);
        $this->assertTrue($this->assessment->questions->contains($question->id));

        // Test dimension relationship (Question model uses find() not relationship)
        $this->assertEquals($this->dimension->id, $question->dimension()->id);
    }

    /**
     * Test question deletion
     */
    public function testQuestionDeletion()
    {
        $this->actingAs($this->user);

        $question = new Question([
            'content' => 'Question to be deleted',
            'number' => 1,
            'type' => 1
        ]);
        $this->assessment->questions()->save($question);

        $questionId = $question->id;

        // Delete question
        $question->delete();

        // Verify question is deleted
        $this->assertNull(Question::find($questionId));
        $this->assertFalse($this->assessment->questions->contains($questionId));
    }

    /**
     * Test question duplication
     */
    public function testQuestionDuplication()
    {
        $this->actingAs($this->user);

        $originalQuestion = new Question([
            'content' => 'Original question',
            'number' => 1,
            'type' => 1,
            'dimension_id' => $this->dimension->id,
            'practice' => false
        ]);
        $this->assessment->questions()->save($originalQuestion);

        // Create duplicate question
        $duplicateQuestion = $originalQuestion->replicate();
        $duplicateQuestion->content = 'Duplicate question';
        $duplicateQuestion->number = 2;
        $this->assessment->questions()->save($duplicateQuestion);

        // Verify duplicate was created
        $this->assertNotNull($duplicateQuestion->id);
        $this->assertEquals('Duplicate question', $duplicateQuestion->content);
        $this->assertEquals(2, $duplicateQuestion->number);
        $this->assertEquals($originalQuestion->type, $duplicateQuestion->type);
        $this->assertEquals($originalQuestion->dimension_id, $duplicateQuestion->dimension_id);
        $this->assertEquals($this->assessment->id, $duplicateQuestion->assessment_id);
    }

    /**
     * Test question filtering by type
     */
    public function testQuestionFilteringByType()
    {
        $this->actingAs($this->user);

        // Create questions of different types
        $questions = [
            [
                'content' => 'Likert question',
                'number' => 1,
                'type' => 1
            ],
            [
                'content' => 'Multiple choice question',
                'number' => 2,
                'type' => 2
            ],
            [
                'content' => 'Text question',
                'number' => 3,
                'type' => 3
            ]
        ];

        foreach ($questions as $questionData) {
            $question = new Question($questionData);
            $this->assessment->questions()->save($question);
        }

        // Test filtering by type
        $likertQuestions = $this->assessment->questions()->where('type', 1)->get();
        $this->assertEquals(1, $likertQuestions->count());
        $this->assertEquals('Likert question', $likertQuestions->first()->content);

        $multipleChoiceQuestions = $this->assessment->questions()->where('type', 2)->get();
        $this->assertEquals(1, $multipleChoiceQuestions->count());
        $this->assertEquals('Multiple choice question', $multipleChoiceQuestions->first()->content);
    }

    /**
     * Test question practice flag functionality
     */
    public function testQuestionPracticeFlag()
    {
        $this->actingAs($this->user);

        // Create practice and non-practice questions
        $practiceQuestion = new Question([
            'content' => 'Practice question',
            'number' => 1,
            'type' => 1,
            'practice' => true
        ]);
        $this->assessment->questions()->save($practiceQuestion);

        $regularQuestion = new Question([
            'content' => 'Regular question',
            'number' => 2,
            'type' => 1,
            'practice' => false
        ]);
        $this->assessment->questions()->save($regularQuestion);

        // Test practice questions filtering
        $practiceQuestions = $this->assessment->questions()->where('practice', true)->get();
        $this->assertEquals(1, $practiceQuestions->count());
        $this->assertEquals('Practice question', $practiceQuestions->first()->content);

        $regularQuestions = $this->assessment->questions()->where('practice', false)->get();
        $this->assertEquals(1, $regularQuestions->count());
        $this->assertEquals('Regular question', $regularQuestions->first()->content);
    }

    /**
     * Test question anchors serialization
     */
    public function testQuestionAnchorsSerialization()
    {
        $this->actingAs($this->user);

        $complexAnchors = [
            'Very Low' => 1,
            'Low' => 2,
            'Medium' => 3,
            'High' => 4,
            'Very High' => 5,
            'Not Applicable' => 0
        ];

        $questionData = [
            'content' => 'Rate your experience level',
            'number' => 1,
            'type' => 2,
            'anchors' => $complexAnchors
        ];

        $question = new Question($questionData);
        $this->assessment->questions()->save($question);

        // Verify anchors are properly serialized/deserialized
        $this->assertTrue(is_array($question->anchors));
        $this->assertEquals(6, count($question->anchors));
        $this->assertEquals(1, $question->anchors['Very Low']);
        $this->assertEquals(5, $question->anchors['Very High']);
        $this->assertEquals(0, $question->anchors['Not Applicable']);
    }

    /**
     * Test question access control
     */
    public function testQuestionAccessControl()
    {
        $this->actingAs($this->user);

        $question = new Question([
            'content' => 'Access control test question',
            'number' => 1,
            'type' => 1
        ]);
        $this->assessment->questions()->save($question);

        // Test that user can access questions in their assessment
        $this->assertTrue($this->assessment->questions->contains($question->id));

        // Create another user and assessment
        $otherUser = User::create([
            'username' => 'otheruser_' . uniqid(),
            'name' => 'Other User',
            'email' => 'other_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'client_id' => $this->client->id,
            'language_id' => $this->language->id
        ]);

        $otherAssessment = new Assessment([
            'name' => 'Other Assessment',
            'description' => 'Other user assessment',
            'target' => 0
        ]);
        $otherUser->assessments()->save($otherAssessment);

        // Test that other user cannot access this question
        $this->assertFalse($otherAssessment->questions->contains($question->id));
    }
}
