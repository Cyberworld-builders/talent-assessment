<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Assignment;
use App\Assessment;
use App\User;
use App\Client;
use App\Job;
use App\Language;
use Bican\Roles\Models\Role;
use Carbon\Carbon;

class AssignmentEmailTest extends TestCase
{
    protected $user;
    protected $client;
    protected $assessment;
    protected $job;
    protected $language;

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
            'name' => 'Test Email Client',
            'require_profile' => true,
            'require_research' => true
        ]);

        $this->job = Job::firstOrCreate([
            'name' => 'Test Email Job',
            'slug' => 'test-email-job-' . uniqid(),
            'client_id' => $this->client->id
        ]);
        
        // Ensure roles exist for testing
        $this->createRolesIfNeeded();
        
        // Create test user with admin role
        $this->user = User::create([
            'username' => 'testemail_' . uniqid(),
            'name' => 'Test Email User',
            'email' => 'email_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'client_id' => $this->client->id,
            'language_id' => $this->language->id,
            'completed_profile' => true,
            'completed_research' => true
        ]);
        
        $adminRole = Role::where('slug', 'admin')->first();
        $this->user->attachRole($adminRole);

        // Create test assessment
        $this->assessment = Assessment::create([
            'name' => 'Test Email Assessment',
            'description' => 'Test assessment for email testing',
            'target' => 0,
            'active' => 1,
            'user_id' => $this->user->id
        ]);
    }

    /**
     * Test email notification field visibility
     */
    public function testEmailNotificationFieldVisibility()
    {
        $this->be($this->user);

        $response = $this->call('GET', "dashboard/clients/{$this->client->id}/assign");

        $this->assertEquals(200, $response->getStatusCode());
        
        // Check that email notification field is present
        $this->assertContains('Email Notification', $response->getContent());
        $this->assertContains('name="send-email"', $response->getContent());
        $this->assertContains('Users with a valid email address will receive a notification', $response->getContent());
    }

    /**
     * Test email preview functionality
     */
    public function testEmailPreviewFunctionality()
    {
        $this->be($this->user);

        $response = $this->call('GET', "dashboard/clients/{$this->client->id}/assign");

        $this->assertEquals(200, $response->getStatusCode());
        
        // Check that email preview elements are present
        $this->assertContains('Email Preview', $response->getContent());
        $this->assertContains('email-subject', $response->getContent());
        $this->assertContains('email-body', $response->getContent());
        $this->assertContains('field-email', $response->getContent());
        $this->assertContains('edit-email-body', $response->getContent());
    }

    /**
     * Test email notification with custom subject
     */
    public function testEmailNotificationWithCustomSubject()
    {
        $this->be($this->user);

        $formData = [
            'assessments' => [$this->assessment->id],
            'user' => [$this->user->id],
            'target' => [0],
            'role' => [''],
            'expiration' => Carbon::tomorrow()->format('D, d M Y'),
            'send-email' => 0,
            'email-subject' => 'Custom Assignment Subject',
            'email-body' => '<p>Custom email body</p>',
            'job_id' => $this->job->id
        ];

        $response = $this->call('POST', "dashboard/clients/{$this->client->id}/assign", $formData);

        $this->assertEquals(302, $response->getStatusCode());
        
        // Verify assignment was created
        $assignment = Assignment::where('user_id', $this->user->id)
            ->where('assessment_id', $this->assessment->id)
            ->first();
        
        $this->assertNotNull($assignment);
    }

    /**
     * Test email notification with custom body
     */
    public function testEmailNotificationWithCustomBody()
    {
        $this->be($this->user);

        $customBody = '<p>Hello [name],</p><p>You have been assigned the following assessment: [assessments]</p><p>Please complete it by [expiration-date].</p><p>Login here: [login-link]</p>';

        $formData = [
            'assessments' => [$this->assessment->id],
            'user' => [$this->user->id],
            'target' => [0],
            'role' => [''],
            'expiration' => Carbon::tomorrow()->format('D, d M Y'),
            'send-email' => 0,
            'email-subject' => 'Custom Assignment Email',
            'email-body' => $customBody,
            'job_id' => $this->job->id
        ];

        $response = $this->call('POST', "dashboard/clients/{$this->client->id}/assign", $formData);

        $this->assertEquals(302, $response->getStatusCode());
        
        // Verify assignment was created
        $assignment = Assignment::where('user_id', $this->user->id)
            ->where('assessment_id', $this->assessment->id)
            ->first();
        
        $this->assertNotNull($assignment);
    }

    /**
     * Test email notification with shortcodes
     */
    public function testEmailNotificationWithShortcodes()
    {
        $this->be($this->user);

        $bodyWithShortcodes = '<p>Hello [name],</p><p>Your username is: [username]</p><p>Your email is: [email]</p><p>Your password is: [password]</p><p>Assessment: [assessments]</p><p>Expires: [expiration-date]</p><p>Login: [login-link]</p>';

        $formData = [
            'assessments' => [$this->assessment->id],
            'user' => [$this->user->id],
            'target' => [0],
            'role' => [''],
            'expiration' => Carbon::tomorrow()->format('D, d M Y'),
            'send-email' => 0,
            'email-subject' => 'Assignment with Shortcodes',
            'email-body' => $bodyWithShortcodes,
            'job_id' => $this->job->id
        ];

        $response = $this->call('POST', "dashboard/clients/{$this->client->id}/assign", $formData);

        $this->assertEquals(302, $response->getStatusCode());
        
        // Verify assignment was created
        $assignment = Assignment::where('user_id', $this->user->id)
            ->where('assessment_id', $this->assessment->id)
            ->first();
        
        $this->assertNotNull($assignment);
    }

    /**
     * Test email notification disabled
     */
    public function testEmailNotificationDisabled()
    {
        $this->be($this->user);

        $formData = [
            'assessments' => [$this->assessment->id],
            'user' => [$this->user->id],
            'target' => [0],
            'role' => [''],
            'expiration' => Carbon::tomorrow()->format('D, d M Y'),
            'send-email' => 0, // Email disabled
            'email-subject' => 'Test Subject',
            'email-body' => '<p>Test body</p>',
            'job_id' => $this->job->id
        ];

        $response = $this->call('POST', "dashboard/clients/{$this->client->id}/assign", $formData);

        $this->assertEquals(302, $response->getStatusCode());
        
        // Verify assignment was created
        $assignment = Assignment::where('user_id', $this->user->id)
            ->where('assessment_id', $this->assessment->id)
            ->first();
        
        $this->assertNotNull($assignment);
    }

    /**
     * Test email notification with default subject
     */
    public function testEmailNotificationWithDefaultSubject()
    {
        $this->be($this->user);

        $formData = [
            'assessments' => [$this->assessment->id],
            'user' => [$this->user->id],
            'target' => [0],
            'role' => [''],
            'expiration' => Carbon::tomorrow()->format('D, d M Y'),
            'send-email' => 0,
            'email-subject' => '', // Empty subject - should use default
            'email-body' => '<p>Default subject test</p>',
            'job_id' => $this->job->id
        ];

        $response = $this->call('POST', "dashboard/clients/{$this->client->id}/assign", $formData);

        $this->assertEquals(302, $response->getStatusCode());
        
        // Verify assignment was created
        $assignment = Assignment::where('user_id', $this->user->id)
            ->where('assessment_id', $this->assessment->id)
            ->first();
        
        $this->assertNotNull($assignment);
    }

    /**
     * Test email notification with multiple users
     */
    public function testEmailNotificationWithMultipleUsers()
    {
        $this->be($this->user);

        // Create additional users
        $user2 = User::create([
            'username' => 'testuser2_' . uniqid(),
            'name' => 'Test User 2',
            'email' => 'user2_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'client_id' => $this->client->id,
            'language_id' => $this->language->id,
            'completed_profile' => true,
            'completed_research' => true
        ]);

        $user3 = User::create([
            'username' => 'testuser3_' . uniqid(),
            'name' => 'Test User 3',
            'email' => 'user3_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'client_id' => $this->client->id,
            'language_id' => $this->language->id,
            'completed_profile' => true,
            'completed_research' => true
        ]);

        $formData = [
            'assessments' => [$this->assessment->id],
            'user' => [$this->user->id, $user2->id, $user3->id],
            'target' => [0, 0, 0],
            'role' => ['', '', ''],
            'expiration' => Carbon::tomorrow()->format('D, d M Y'),
            'send-email' => 0,
            'email-subject' => 'Bulk Assignment Email',
            'email-body' => '<p>Bulk assignment email body</p>',
            'job_id' => $this->job->id
        ];

        $response = $this->call('POST', "dashboard/clients/{$this->client->id}/assign", $formData);

        $this->assertEquals(302, $response->getStatusCode());
        
        // Verify assignments were created for all users
        $assignments = Assignment::where('assessment_id', $this->assessment->id)->get();
        $this->assertCount(3, $assignments);
        
        $userIds = $assignments->pluck('user_id')->toArray();
        $this->assertContains($this->user->id, $userIds);
        $this->assertContains($user2->id, $userIds);
        $this->assertContains($user3->id, $userIds);
    }

    /**
     * Test email notification with invalid email addresses
     */
    public function testEmailNotificationWithInvalidEmailAddresses()
    {
        $this->be($this->user);

        // Create user with invalid email
        $userWithInvalidEmail = User::create([
            'username' => 'invalidemail_' . uniqid(),
            'name' => 'Invalid Email User',
            'email' => 'invalid-email-format', // Invalid email format
            'password' => bcrypt('password'),
            'client_id' => $this->client->id,
            'language_id' => $this->language->id,
            'completed_profile' => true,
            'completed_research' => true
        ]);

        $formData = [
            'assessments' => [$this->assessment->id],
            'user' => [$userWithInvalidEmail->id],
            'target' => [0],
            'role' => [''],
            'expiration' => Carbon::tomorrow()->format('D, d M Y'),
            'send-email' => 0,
            'email-subject' => 'Test Invalid Email',
            'email-body' => '<p>Test email body</p>',
            'job_id' => $this->job->id
        ];

        $response = $this->call('POST', "dashboard/clients/{$this->client->id}/assign", $formData);

        // Should still create assignment even with invalid email
        $this->assertEquals(302, $response->getStatusCode());
        
        // Verify assignment was created
        $assignment = Assignment::where('user_id', $userWithInvalidEmail->id)
            ->where('assessment_id', $this->assessment->id)
            ->first();
        
        $this->assertNotNull($assignment);
    }

    /**
     * Test email notification with empty email body
     */
    public function testEmailNotificationWithEmptyEmailBody()
    {
        $this->be($this->user);

        $formData = [
            'assessments' => [$this->assessment->id],
            'user' => [$this->user->id],
            'target' => [0],
            'role' => [''],
            'expiration' => Carbon::tomorrow()->format('D, d M Y'),
            'send-email' => 0,
            'email-subject' => 'Empty Body Test',
            'email-body' => '', // Empty body
            'job_id' => $this->job->id
        ];

        $response = $this->call('POST', "dashboard/clients/{$this->client->id}/assign", $formData);

        $this->assertEquals(302, $response->getStatusCode());
        
        // Verify assignment was created
        $assignment = Assignment::where('user_id', $this->user->id)
            ->where('assessment_id', $this->assessment->id)
            ->first();
        
        $this->assertNotNull($assignment);
    }

    /**
     * Test email notification with HTML content
     */
    public function testEmailNotificationWithHtmlContent()
    {
        $this->be($this->user);

        $htmlBody = '<h1>Assignment Notification</h1><p><strong>Hello [name],</strong></p><p>You have been assigned the following assessment:</p><ul><li>[assessments]</li></ul><p><em>Please complete it by [expiration-date].</em></p><p><a href="[login-link]">Click here to login</a></p>';

        $formData = [
            'assessments' => [$this->assessment->id],
            'user' => [$this->user->id],
            'target' => [0],
            'role' => [''],
            'expiration' => Carbon::tomorrow()->format('D, d M Y'),
            'send-email' => 0,
            'email-subject' => 'HTML Email Test',
            'email-body' => $htmlBody,
            'job_id' => $this->job->id
        ];

        $response = $this->call('POST', "dashboard/clients/{$this->client->id}/assign", $formData);

        $this->assertEquals(302, $response->getStatusCode());
        
        // Verify assignment was created
        $assignment = Assignment::where('user_id', $this->user->id)
            ->where('assessment_id', $this->assessment->id)
            ->first();
        
        $this->assertNotNull($assignment);
    }

    /**
     * Test email notification with special characters
     */
    public function testEmailNotificationWithSpecialCharacters()
    {
        $this->be($this->user);

        $specialCharBody = '<p>Hello [name],</p><p>Special characters: &amp; &lt; &gt; &quot; &#39;</p><p>Unicode: ñáéíóú çüö</p><p>Assessment: [assessments]</p>';

        $formData = [
            'assessments' => [$this->assessment->id],
            'user' => [$this->user->id],
            'target' => [0],
            'role' => [''],
            'expiration' => Carbon::tomorrow()->format('D, d M Y'),
            'send-email' => 0,
            'email-subject' => 'Special Characters Test',
            'email-body' => $specialCharBody,
            'job_id' => $this->job->id
        ];

        $response = $this->call('POST', "dashboard/clients/{$this->client->id}/assign", $formData);

        $this->assertEquals(302, $response->getStatusCode());
        
        // Verify assignment was created
        $assignment = Assignment::where('user_id', $this->user->id)
            ->where('assessment_id', $this->assessment->id)
            ->first();
        
        $this->assertNotNull($assignment);
    }

    /**
     * Helper method to create roles if they don't exist
     */
    private function createRolesIfNeeded()
    {
        $roles = ['admin', 'client', 'user'];
        
        foreach ($roles as $roleName) {
            if (!Role::where('slug', $roleName)->exists()) {
                Role::create([
                    'name' => ucfirst($roleName),
                    'slug' => $roleName,
                    'level' => $roleName === 'admin' ? 3 : ($roleName === 'client' ? 2 : 1)
                ]);
            }
        }
    }
}
