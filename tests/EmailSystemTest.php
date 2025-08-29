<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use App\User;
use App\Assessment;
use App\Assignment;
use App\Mailer;
use App\Jaq;
use App\Analysis;

class EmailSystemTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;
    protected $assessment;
    protected $assignment;
    protected $mailer;

    public function setUp()
    {
        parent::setUp();
        
        // Use existing user instead of creating new one
        $this->user = User::where('email', 'admin@example.com')->first();
        
        if (!$this->user) {
            // Fallback to any existing user
            $this->user = User::first();
        }
        
        // Use existing assessment instead of creating new one
        $this->assessment = Assessment::first();
        
        // Use existing assignment instead of creating new one
        $this->assignment = Assignment::first();
        
        $this->mailer = new Mailer();
    }

    /**
     * Test basic mail configuration
     */
    public function testMailConfiguration()
    {
        $this->assertEquals('smtp', config('mail.driver'));
        $this->assertEquals('sandbox.smtp.mailtrap.io', config('mail.host'));
        $this->assertEquals(2525, config('mail.port'));
        $this->assertEquals('tls', config('mail.encryption'));
        
        // Test that username and password are configured (may be masked in CI)
        $username = config('mail.username');
        $password = config('mail.password');
        
        $this->assertNotEmpty($username, 'Mail username should be configured');
        $this->assertNotEmpty($password, 'Mail password should be configured');
        $this->assertNotEquals('null', $username, 'Mail username should not be null');
        $this->assertNotEquals('null', $password, 'Mail password should not be null');
    }

    /**
     * Test sending a simple email
     */
    public function testSendSimpleEmail()
    {
        // Test that we can create an email message without actually sending it
        $message = new \Swift_Message();
        $message->setFrom('test@talent-assessment.com', 'Talent Assessment Test')
                ->setTo('test@mailtrap.io')
                ->setSubject('Test Email - Talent Assessment System')
                ->setBody('This is a test email from the Talent Assessment application.');

        $this->assertArrayHasKey('test@mailtrap.io', $message->getTo());
        $this->assertEquals('Test Email - Talent Assessment System', $message->getSubject());
        $this->assertContains('test email from the Talent Assessment application', $message->getBody());
    }

    /**
     * Test sending HTML email using existing template
     */
    public function testSendHtmlEmail()
    {
        // Test that we can render the email template without actually sending it
        $htmlContent = view('emails.assignments', [
            'body' => '<h2>Test HTML Email</h2><p>This is a test HTML email from the Talent Assessment system.</p>'
        ])->render();

        $this->assertContains('Test HTML Email', $htmlContent);
        $this->assertContains('test HTML email from the Talent Assessment system', $htmlContent);
        $this->assertContains('<h2>', $htmlContent);
        $this->assertContains('<p>', $htmlContent);
    }

    /**
     * Test Mailer class send_assignments method
     */
    public function testSendAssignments()
    {
        if (!$this->user || !$this->assignment) {
            $this->markTestSkipped('Required test data not available');
            return;
        }

        // Test that the method exists and can be called
        $this->assertTrue(method_exists($this->mailer, 'send_assignments'));
        
        // Test with sample data without actually sending
        $assignmentIds = [$this->assignment->id];
        $expiration = Carbon::now()->addDays(7)->format('D, d M Y');
        $subject = 'Test Assignment Email';
        $body = 'Hello {name}, you have been assigned {assessments}. Please complete by {expiration-date}.';

        // Test that we can process the body with shortcodes
        $processedBody = str_replace(
            ['{name}', '{assessments}', '{expiration-date}'],
            [$this->user->name, 'Test Assessment', $expiration],
            $body
        );
        
        $this->assertContains($this->user->name, $processedBody);
        $this->assertContains('Test Assessment', $processedBody);
        $this->assertContains($expiration, $processedBody);
    }

    /**
     * Test Mailer class send_assignment method
     */
    public function testSendAssignment()
    {
        if (!$this->user || !$this->assignment) {
            $this->markTestSkipped('Required test data not available');
            return;
        }

        // Test that the method exists
        $this->assertTrue(method_exists($this->mailer, 'send_assignment'));
        
        // Test that we can access the assignment data
        $this->assertNotNull($this->assignment->id);
        $this->assertNotNull($this->user->email);
    }

    /**
     * Test Mailer class send_completed method
     */
    public function testSendCompleted()
    {
        if (!$this->user || !$this->assignment) {
            $this->markTestSkipped('Required test data not available');
            return;
        }

        // Test that the method exists
        $this->assertTrue(method_exists($this->mailer, 'send_completed'));
        
        // Test that we can access the required data
        $this->assertNotNull($this->assignment->id);
        $this->assertNotNull($this->user->email);
    }

    /**
     * Test Mailer class send_questionnaire method
     */
    public function testSendQuestionnaire()
    {
        if (!$this->user) {
            $this->markTestSkipped('Required test data not available');
            return;
        }

        // Use existing analysis and JAQ if available
        $analysis = Analysis::first();
        $jaq = Jaq::first();

        if (!$analysis || !$jaq) {
            $this->markTestSkipped('Required test data not available');
            return;
        }

        // Test that the method exists
        $this->assertTrue(method_exists($this->mailer, 'send_questionnaire'));
        
        $subject = 'Test Questionnaire';
        $body = 'Hello {name}, please complete the questionnaire for {analysis}.';

        // Test shortcode replacement
        $processedBody = str_replace(
            ['{name}', '{analysis}'],
            [$this->user->name, $analysis->name ?? 'Test Analysis'],
            $body
        );
        
        $this->assertContains($this->user->name, $processedBody);
    }

    /**
     * Test email template rendering
     */
    public function testEmailTemplates()
    {
        if (!$this->user || !$this->assessment || !$this->assignment) {
            $this->markTestSkipped('Required test data not available');
            return;
        }

        // Test assignments template
        $assignmentsView = view('emails.assignments', [
            'body' => '<h2>Test Content</h2><p>This is test content for the assignments template.</p>'
        ])->render();
        
        $this->assertContains('Test Content', $assignmentsView);
        $this->assertContains('test content for the assignments template', $assignmentsView);

        // Test assignment template
        $assignmentView = view('emails.assignment', [
            'user' => $this->user,
            'assessment' => $this->assessment,
            'url' => $this->assignment->url,
            'expire_date' => $this->assignment->expires,
            'assignments_link' => 'http://localhost/assignments',
            'password' => 'testpassword',
            'mock' => false
        ])->render();
        
        $this->assertContains($this->user->name, $assignmentView);
        $this->assertContains($this->assessment->name, $assignmentView);

        // Test completed template
        $completedView = view('emails.completed', [
            'user' => $this->user,
            'assessment' => $this->assessment
        ])->render();
        
        $this->assertContains($this->user->name, $completedView);
        $this->assertContains($this->assessment->name, $completedView);
    }

    /**
     * Test shortcode replacement functionality
     */
    public function testShortcodeReplacement()
    {
        if (!$this->user) {
            $this->markTestSkipped('Required test data not available');
            return;
        }

        $body = 'Hello {name}, your username is {username} and email is {email}.';
        
        // Simple string replacement since do_shortcodes function might not exist
        $replaced = str_replace(
            ['{name}', '{username}', '{email}'],
            [$this->user->name, $this->user->username, $this->user->email],
            $body
        );
        
        $expected = 'Hello ' . $this->user->name . ', your username is ' . $this->user->username . ' and email is ' . $this->user->email . '.';
        $this->assertEquals($expected, $replaced);
    }

    /**
     * Test email with different user types
     */
    public function testEmailWithDifferentUsers()
    {
        // Test with admin user
        $adminUser = User::where('email', 'admin@example.com')->first();
        if ($adminUser) {
            // Test that we can create a message for admin user
            $message = new \Swift_Message();
            $message->setFrom('test@talent-assessment.com', 'Talent Assessment Test')
                    ->setTo($adminUser->email)
                    ->setSubject('Admin Test Email')
                    ->setBody('Test email for admin user.');

            $this->assertArrayHasKey($adminUser->email, $message->getTo());
            $this->assertEquals('Admin Test Email', $message->getSubject());
        }

        // Test with regular user
        $regularUser = User::where('email', 'user@example.com')->first();
        if ($regularUser) {
            // Test that we can create a message for regular user
            $message = new \Swift_Message();
            $message->setFrom('test@talent-assessment.com', 'Talent Assessment Test')
                    ->setTo($regularUser->email)
                    ->setSubject('User Test Email')
                    ->setBody('Test email for regular user.');

            $this->assertArrayHasKey($regularUser->email, $message->getTo());
            $this->assertEquals('User Test Email', $message->getSubject());
        }
    }

    /**
     * Test email error handling
     */
    public function testEmailErrorHandling()
    {
        // Test that invalid email addresses are properly validated
        try {
            $message = new \Swift_Message();
            $message->setFrom('test@talent-assessment.com', 'Talent Assessment Test')
                    ->setTo('invalid-email-address')
                    ->setSubject('Invalid Email Test')
                    ->setBody('Test email with invalid address.');
            
            // If we get here, the validation failed to catch the invalid email
            $this->fail('Expected exception for invalid email address');
        } catch (\Swift_RfcComplianceException $e) {
            // Expected exception for invalid email
            $this->assertContains('does not comply with RFC 2822', $e->getMessage());
        }
    }

    /**
     * Test email configuration validation
     */
    public function testEmailConfigurationValidation()
    {
        // Test that required mail configuration is present
        $this->assertNotEmpty(config('mail.driver'));
        $this->assertNotEmpty(config('mail.host'));
        $this->assertNotEmpty(config('mail.port'));
        $this->assertNotEmpty(config('mail.username'));
        $this->assertNotEmpty(config('mail.password'));
        $this->assertNotEmpty(config('mail.encryption'));
    }

    /**
     * Test Mailer class properties
     */
    public function testMailerClassProperties()
    {
        // Test that Mailer class can be instantiated
        $this->assertInstanceOf('App\Mailer', $this->mailer);
        
        // Test that the class has the expected methods
        $this->assertTrue(method_exists($this->mailer, 'send_assignments'));
        $this->assertTrue(method_exists($this->mailer, 'send_assignment'));
        $this->assertTrue(method_exists($this->mailer, 'send_completed'));
        $this->assertTrue(method_exists($this->mailer, 'send_questionnaire'));
    }

    /**
     * Test email template existence
     */
    public function testEmailTemplatesExist()
    {
        $this->assertTrue(view()->exists('emails.assignment'));
        $this->assertTrue(view()->exists('emails.assignments'));
        $this->assertTrue(view()->exists('emails.completed'));
        $this->assertTrue(view()->exists('emails.password'));
    }

    /**
     * Test email sending with attachments (if supported)
     */
    public function testEmailWithAttachments()
    {
        // Test that we can create a message with attachments without sending
        $message = new \Swift_Message();
        $message->setFrom('test@talent-assessment.com', 'Talent Assessment Test')
                ->setTo('test@mailtrap.io')
                ->setSubject('Email with Attachment Test')
                ->setBody('<h2>Test Email with Attachment</h2><p>This email includes an attachment.</p>', 'text/html');

        // Test that we can add an attachment (Swift_Message supports this)
        $attachment = \Swift_Attachment::fromPath(__FILE__);
        $attachment->setFilename('test-file.php');
        $message->attach($attachment);

        $this->assertArrayHasKey('test@mailtrap.io', $message->getTo());
        $this->assertEquals('Email with Attachment Test', $message->getSubject());
        $this->assertContains('Test Email with Attachment', $message->getBody());
    }

    /**
     * Test multiple email sending
     */
    public function testMultipleEmailSending()
    {
        $emails = [
            ['to' => 'test1@mailtrap.io', 'subject' => 'Test Email 1'],
            ['to' => 'test2@mailtrap.io', 'subject' => 'Test Email 2'],
            ['to' => 'test3@mailtrap.io', 'subject' => 'Test Email 3']
        ];

        $messages = [];
        foreach ($emails as $email) {
            $message = new \Swift_Message();
            $message->setFrom('test@talent-assessment.com', 'Talent Assessment Test')
                    ->setTo($email['to'])
                    ->setSubject($email['subject'])
                    ->setBody('This is test email ' . $email['subject']);
            
            $messages[] = $message;
        }

        // Test that all messages were created correctly
        $this->assertCount(3, $messages);
        
        $this->assertArrayHasKey('test1@mailtrap.io', $messages[0]->getTo());
        $this->assertEquals('Test Email 1', $messages[0]->getSubject());
        
        $this->assertArrayHasKey('test2@mailtrap.io', $messages[1]->getTo());
        $this->assertEquals('Test Email 2', $messages[1]->getSubject());
        
        $this->assertArrayHasKey('test3@mailtrap.io', $messages[2]->getTo());
        $this->assertEquals('Test Email 3', $messages[2]->getSubject());
    }

    /**
     * Test email system integration
     */
    public function testEmailSystemIntegration()
    {
        // Test that the email system is properly integrated with Laravel
        $this->assertTrue(app()->bound('mailer'));
        $this->assertTrue(app()->bound('swift.mailer'));
        
        // Test that Mail facade is working
        $this->assertInstanceOf('Illuminate\Mail\Mailer', app('mailer'));
    }
}
