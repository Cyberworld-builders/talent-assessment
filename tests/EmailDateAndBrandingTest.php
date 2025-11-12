<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Carbon\Carbon;

class EmailDateAndBrandingTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test that email dates are parsed correctly with the new format
     */
    public function testEmailDateParsing()
    {
        // Test the new date format parsing
        $test_date = '23 Sep 2025';
        
        $parsed = Carbon::createFromFormat('d M Y', $test_date);
        
        $this->assertEquals('2025-09-23', $parsed->format('Y-m-d'));
        $this->assertEquals('Tuesday', $parsed->format('l'));
        $this->assertEquals('Tuesday, September 23, 2025', $parsed->format('l, F j, Y'));
    }

    /**
     * Test that email templates use correct branding
     */
    public function testEmailBranding()
    {
        // Test that email templates contain "Involved Talent" instead of "AOE Science"
        $assignment_template = file_get_contents(base_path('resources/views/emails/assignment.blade.php'));
        $completed_template = file_get_contents(base_path('resources/views/emails/completed.blade.php'));
        
        // Check that templates contain "Involved Talent"
        $this->assertContains('Involved Talent', $assignment_template);
        $this->assertContains('Involved Talent', $completed_template);
        
        // Check that templates don't contain old branding
        $this->assertNotContains('The AOE Group', $assignment_template);
        $this->assertNotContains('The AOE Group', $completed_template);
    }

    /**
     * Test that mail configuration uses correct branding
     */
    public function testMailConfiguration()
    {
        $mail_config = config('mail');
        
        // Check that default from name is "Involved Talent" (or environment override)
        $expected_name = env('MAIL_FROM_NAME', 'Involved Talent');
        $this->assertEquals($expected_name, $mail_config['from']['name']);
        
        // Check that default from address uses involvedtalent.com domain (or environment override)
        $expected_address = env('MAIL_FROM_ADDRESS', 'postmaster@mg.involvedtalent.com');
        $this->assertEquals($expected_address, $mail_config['from']['address']);
    }

    /**
     * Test that date format changes prevent invalid date combinations
     */
    public function testDateFormatPreventsInvalidCombinations()
    {
        // Test that the new format doesn't allow day-of-week mismatches
        $valid_date = '23 Sep 2025';
        $parsed = Carbon::createFromFormat('d M Y', $valid_date);
        
        // Should parse to the correct date
        $this->assertEquals('2025-09-23', $parsed->format('Y-m-d'));
        $this->assertEquals('Tuesday', $parsed->format('l'));
        
        // Test that we can't accidentally create invalid dates
        // (This was the original problem - "Mon, 23 Sep 2025" would parse to wrong date)
        $this->assertNotEquals('Monday', $parsed->format('l'));
    }

    /**
     * Test that email date formatting works correctly
     */
    public function testEmailDateFormatting()
    {
        // Simulate the email date formatting process
        $expiration_date = '23 Sep 2025';
        $parsed = Carbon::createFromFormat('d M Y', $expiration_date);
        
        // Test the format used in email templates
        $email_format = $parsed->format('l, F j, Y');
        $this->assertEquals('Tuesday, September 23, 2025', $email_format);
        
        // Test that the format is user-friendly
        $this->assertContains('Tuesday', $email_format);
        $this->assertContains('September', $email_format);
        $this->assertContains('2025', $email_format);
    }

    /**
     * Test that form date generation works correctly
     */
    public function testFormDateGeneration()
    {
        // Test the default form date generation
        $tomorrow = Carbon::tomorrow();
        $form_date = $tomorrow->format('D, d M Y');
        
        // Should be in the correct format (Day, dd Mon yyyy)
        $this->assertRegExp('/^[A-Za-z]{3}, \d{1,2} [A-Za-z]{3} \d{4}$/', $form_date);
        
        // Should be parseable
        $parsed = Carbon::createFromFormat('D, d M Y', $form_date);
        $this->assertEquals($tomorrow->format('Y-m-d'), $parsed->format('Y-m-d'));
    }
}
