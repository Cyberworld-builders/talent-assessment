<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Assessment;
use App\Question;
use App\Dimension;
use App\User;
use App\Client;
use App\Language;
use Bican\Roles\Models\Role;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class AssessmentManagementTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;
    protected $client;
    protected $language;
    protected $dimension;

    public function setUp()
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
    // ASSESSMENT CREATION TESTS
    // ========================================

    /**
     * Test basic assessment creation with required fields
     */
    public function testBasicAssessmentCreation()
    {
        $this->actingAs($this->user);

        $assessmentData = [
            'name' => 'Test Assessment',
            'description' => 'This is a test assessment for validation',
            'paginate' => 10,
            'items_per_page' => 5,
            'timed' => 1,
            'time_limit' => 30,
            'target' => 0, // Self
            'use_custom_fields' => 0
        ];

        $assessment = new Assessment($assessmentData);
        $this->user->assessments()->save($assessment);

        // Verify assessment was created
        $this->assertNotNull($assessment->id);
        $this->assertEquals('Test Assessment', $assessment->name);
        $this->assertEquals($this->user->id, $assessment->user_id);
        $this->assertEquals(30, $assessment->time_limit);
        $this->assertEquals(0, $assessment->target);
        $this->assertFalse((bool)$assessment->use_custom_fields);
    }

    /**
     * Test assessment creation with custom fields for "Other" target
     */
    public function testAssessmentCreationWithCustomFieldsForOther()
    {
        $this->actingAs($this->user);

        $assessmentData = [
            'name' => 'Other Assessment',
            'description' => 'Assessment for evaluating others',
            'target' => 1, // Other User
            'use_custom_fields' => 1,
            'custom_fields' => [
                'tag' => ['name', 'email'],
                'default' => ['', '']
            ]
        ];

        $assessment = new Assessment($assessmentData);
        $this->user->assessments()->save($assessment);

        // Verify custom fields were set correctly
        $this->assertEquals(1, $assessment->target);
        $this->assertTrue((bool)$assessment->use_custom_fields);
        $this->assertTrue(is_array($assessment->custom_fields));
        $this->assertEquals(['name', 'email'], $assessment->custom_fields['tag']);
    }

    /**
     * Test assessment creation with custom fields for "Group Leader" target
     */
    public function testAssessmentCreationWithCustomFieldsForGroupLeader()
    {
        $this->actingAs($this->user);

        $assessmentData = [
            'name' => 'Group Leader Assessment',
            'description' => 'Assessment for group leaders',
            'target' => 2, // Group Leader
            'use_custom_fields' => 1,
            'custom_fields' => [
                'tag' => ['name', 'email', 'grouprole'],
                'default' => ['', '', '']
            ]
        ];

        $assessment = new Assessment($assessmentData);
        $this->user->assessments()->save($assessment);

        // Verify group leader custom fields
        $this->assertEquals(2, $assessment->target);
        $this->assertTrue((bool)$assessment->use_custom_fields);
        $this->assertContains('grouprole', $assessment->custom_fields['tag']);
    }

    /**
     * Test assessment creation with branding assets
     */
    public function testAssessmentCreationWithBranding()
    {
        $this->actingAs($this->user);

        $assessmentData = [
            'name' => 'Branded Assessment',
            'description' => 'Assessment with custom branding',
            'logo' => 'https://example.com/logo.png',
            'background' => 'https://example.com/background.jpg',
            'whitelabel' => 1,
            'company_labeled_for' => 'Test Company',
            'target' => 0
        ];

        $assessment = new Assessment($assessmentData);
        $this->user->assessments()->save($assessment);

        // Verify branding was saved
        $this->assertEquals('https://example.com/logo.png', $assessment->logo);
        $this->assertEquals('https://example.com/background.jpg', $assessment->background);
        $this->assertTrue((bool)$assessment->whitelabel);
        $this->assertEquals('Test Company', $assessment->company_labeled_for);
    }

    /**
     * Test assessment creation with multi-language support
     */
    public function testAssessmentCreationWithTranslation()
    {
        $this->actingAs($this->user);

        $assessmentData = [
            'name' => 'Multi-language Assessment',
            'description' => 'Assessment with translation support',
            'translation' => 1,
            'language' => 'en,es,fr',
            'target' => 0
        ];

        $assessment = new Assessment($assessmentData);
        $this->user->assessments()->save($assessment);

        // Verify translation settings
        $this->assertTrue((bool)$assessment->translation);
        $this->assertEquals('en,es,fr', $assessment->language);
    }

    // ========================================
    // ASSESSMENT CONFIGURATION TESTS
    // ========================================

    /**
     * Test adding questions to assessment
     */
    public function testAddingQuestionsToAssessment()
    {
        $this->actingAs($this->user);

        $assessment = new Assessment([
            'name' => 'Question Test Assessment',
            'description' => 'Testing question management',
            'target' => 0
        ]);
        $this->user->assessments()->save($assessment);

        // Create questions
        $questions = [
            [
                'content' => 'How do you rate your communication skills?',
                'number' => 1,
                'type' => 1, // Likert
                'dimension_id' => $this->dimension->id,
                'practice' => false
            ],
            [
                'content' => 'How do you handle stress?',
                'number' => 2,
                'type' => 1, // Likert
                'dimension_id' => $this->dimension->id,
                'practice' => false
            ]
        ];

        foreach ($questions as $questionData) {
            $question = new Question($questionData);
            $assessment->questions()->save($question);
        }

        // Verify questions were added
        $this->assertEquals(2, $assessment->questions()->count());
        $this->assertEquals('How do you rate your communication skills?', $assessment->questions()->first()->content);
        $this->assertEquals($this->dimension->id, $assessment->questions()->first()->dimension_id);
    }

    /**
     * Test question filtering (excluding descriptors)
     */
    public function testQuestionFiltering()
    {
        $this->actingAs($this->user);

        $assessment = new Assessment([
            'name' => 'Filter Test Assessment',
            'description' => 'Testing question filtering',
            'target' => 0
        ]);
        $this->user->assessments()->save($assessment);

        // Create different types of questions
        $questions = [
            [
                'content' => 'Regular question',
                'number' => 1,
                'type' => 1, // Regular question
                'dimension_id' => $this->dimension->id
            ],
            [
                'content' => 'Descriptor question',
                'number' => 2,
                'type' => 2, // Descriptor (should be filtered out)
                'dimension_id' => $this->dimension->id
            ],
            [
                'content' => 'Another regular question',
                'number' => 3,
                'type' => 1, // Regular question
                'dimension_id' => $this->dimension->id
            ]
        ];

        foreach ($questions as $questionData) {
            $question = new Question($questionData);
            $assessment->questions()->save($question);
        }

        // Test filtered questions (should exclude type 2)
        $filteredQuestions = $assessment->filteredQuestions();
        $this->assertEquals(2, $filteredQuestions->count());
        $this->assertNotContains(2, $filteredQuestions->pluck('type'));
    }

    /**
     * Test assessment timing configuration
     */
    public function testAssessmentTimingConfiguration()
    {
        $this->actingAs($this->user);

        $assessment = new Assessment([
            'name' => 'Timed Assessment',
            'description' => 'Assessment with time limits',
            'timed' => 1,
            'time_limit' => 45,
            'target' => 0
        ]);
        $this->user->assessments()->save($assessment);

        // Verify timing configuration
        $this->assertTrue((bool)$assessment->timed);
        $this->assertEquals(45, $assessment->time_limit);
    }

    /**
     * Test assessment pagination configuration
     */
    public function testAssessmentPaginationConfiguration()
    {
        $this->actingAs($this->user);

        $assessment = new Assessment([
            'name' => 'Paginated Assessment',
            'description' => 'Assessment with pagination',
            'paginate' => 1,
            'items_per_page' => 3,
            'target' => 0
        ]);
        $this->user->assessments()->save($assessment);

        // Verify pagination configuration
        $this->assertTrue((bool)$assessment->paginate);
        $this->assertEquals(3, $assessment->items_per_page);
    }

    // ========================================
    // ASSESSMENT MANAGEMENT TESTS
    // ========================================

    /**
     * Test assessment editing and updates
     */
    public function testAssessmentEditing()
    {
        $this->actingAs($this->user);

        $assessment = new Assessment([
            'name' => 'Original Name',
            'description' => 'Original description',
            'target' => 0
        ]);
        $this->user->assessments()->save($assessment);

        // Update assessment
        $assessment->update([
            'name' => 'Updated Name',
            'description' => 'Updated description',
            'time_limit' => 60
        ]);

        // Refresh from database
        $assessment = Assessment::find($assessment->id);

        // Verify updates
        $this->assertEquals('Updated Name', $assessment->name);
        $this->assertEquals('Updated description', $assessment->description);
        $this->assertEquals(60, $assessment->time_limit);
    }

    /**
     * Test assessment relationships
     */
    public function testAssessmentRelationships()
    {
        $this->actingAs($this->user);

        $assessment = new Assessment([
            'name' => 'Relationship Test Assessment',
            'description' => 'Testing relationships',
            'target' => 0
        ]);
        $this->user->assessments()->save($assessment);

        // Test user relationship
        $this->assertEquals($this->user->id, $assessment->user->id);
        $this->assertTrue($this->user->assessments->contains($assessment->id));

        // Test questions relationship
        $question = new Question([
            'content' => 'Test question',
            'number' => 1,
            'type' => 1,
            'dimension_id' => $this->dimension->id
        ]);
        $assessment->questions()->save($question);

        $this->assertEquals(1, $assessment->questions()->count());
        $this->assertEquals($assessment->id, $question->assessment_id);

        // Test dimensions relationship
        $dimension = new Dimension([
            'name' => 'Test Dimension',
            'parent' => 0,
            'code' => 'TEST'
        ]);
        $assessment->dimensions()->save($dimension);

        $this->assertEquals(1, $assessment->dimensions()->count());
    }

    /**
     * Test assessment deletion and cleanup
     */
    public function testAssessmentDeletion()
    {
        $this->actingAs($this->user);

        $assessment = new Assessment([
            'name' => 'Delete Test Assessment',
            'description' => 'Assessment to be deleted',
            'target' => 0
        ]);
        $this->user->assessments()->save($assessment);

        // Add a question to test cascade deletion
        $question = new Question([
            'content' => 'Test question',
            'number' => 1,
            'type' => 1,
            'dimension_id' => $this->dimension->id
        ]);
        $assessment->questions()->save($question);

        $assessmentId = $assessment->id;
        $questionId = $question->id;

        // Delete assessment
        $assessment->delete();

        // Verify assessment is deleted
        $this->assertNull(Assessment::find($assessmentId));

        // Verify related questions are also deleted
        $this->assertNull(Question::find($questionId));
    }

    /**
     * Test assessment duplication
     */
    public function testAssessmentDuplication()
    {
        $this->actingAs($this->user);

        $originalAssessment = new Assessment([
            'name' => 'Original Assessment',
            'description' => 'Original description',
            'target' => 0,
            'time_limit' => 30
        ]);
        $this->user->assessments()->save($originalAssessment);

        // Add a question to the original
        $question = new Question([
            'content' => 'Original question',
            'number' => 1,
            'type' => 1,
            'dimension_id' => $this->dimension->id
        ]);
        $originalAssessment->questions()->save($question);

        // Create duplicate assessment
        $duplicateAssessment = $originalAssessment->replicate();
        $duplicateAssessment->name = 'Duplicate Assessment';
        $duplicateAssessment->save();

        // Verify duplicate was created
        $this->assertNotNull($duplicateAssessment->id);
        $this->assertEquals('Duplicate Assessment', $duplicateAssessment->name);
        $this->assertEquals('Original description', $duplicateAssessment->description);
        $this->assertEquals(30, $duplicateAssessment->time_limit);
        $this->assertEquals($this->user->id, $duplicateAssessment->user_id);
    }

    /**
     * Test assessment validation
     */
    public function testAssessmentValidation()
    {
        $this->actingAs($this->user);

        // Test required fields
        $this->setExpectedException(\Illuminate\Database\QueryException::class);
        
        Assessment::create([
            // Missing required fields
            'target' => 0
        ]);
    }

    /**
     * Test assessment custom fields serialization
     */
    public function testCustomFieldsSerialization()
    {
        $this->actingAs($this->user);

        $customFields = [
            'tag' => ['name', 'email', 'department'],
            'default' => ['', '', ''],
            'required' => [true, true, false]
        ];

        $assessment = new Assessment([
            'name' => 'Custom Fields Test',
            'description' => 'Testing custom fields',
            'target' => 1,
            'use_custom_fields' => 1,
            'custom_fields' => $customFields
        ]);
        $this->user->assessments()->save($assessment);

        // Verify custom fields are properly serialized/deserialized
        $this->assertTrue(is_array($assessment->custom_fields));
        $this->assertEquals(['name', 'email', 'department'], $assessment->custom_fields['tag']);
        $this->assertEquals([true, true, false], $assessment->custom_fields['required']);
    }

    /**
     * Test assessment last_modified tracking
     */
    public function testLastModifiedTracking()
    {
        $this->actingAs($this->user);

        $assessment = new Assessment([
            'name' => 'Modified Tracking Test',
            'description' => 'Testing last_modified tracking',
            'target' => 0
        ]);
        $this->user->assessments()->save($assessment);

        $originalModified = $assessment->last_modified;

        // Wait a moment to ensure timestamp difference
        sleep(1);

        // Update assessment
        $assessment->update(['name' => 'Updated Name']);

        // Refresh from database
        $assessment = Assessment::find($assessment->id);

        // Verify last_modified was updated
        $this->assertGreaterThan($originalModified, $assessment->last_modified);
    }

    /**
     * Test assessment access control
     */
    public function testAssessmentAccessControl()
    {
        $this->actingAs($this->user);

        $assessment = new Assessment([
            'name' => 'Access Control Test',
            'description' => 'Testing access control',
            'target' => 0
        ]);
        $this->user->assessments()->save($assessment);

        // Test that user can access their own assessment
        $this->assertTrue($this->user->assessments->contains($assessment->id));

        // Create another user and test they cannot access this assessment
        $otherUser = User::create([
            'username' => 'otheruser_' . uniqid(),
            'name' => 'Other User',
            'email' => 'other_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'client_id' => $this->client->id,
            'language_id' => $this->language->id
        ]);

        $this->assertFalse($otherUser->assessments->contains($assessment->id));
    }
}
