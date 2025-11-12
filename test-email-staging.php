<?php

/**
 * Test Email Script for Staging
 * 
 * This script sends a test email to verify the email system is working correctly on staging.
 * Usage: docker exec talent-assessment-app-staging php test-email-staging.php
 */

// Bootstrap Laravel
require __DIR__.'/bootstrap/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\User;
use App\Assignment;
use App\Mailer;

echo "=== Email Test Script for Staging ===\n\n";

// Test 1: Send assignment email
echo "Test 1: Sending assignment email...\n";

try {
    $user = User::find(2); // User Apone
    if (!$user) {
        echo "Error: User not found\n";
        exit(1);
    }
    
    echo "  User: {$user->name} ({$user->email})\n";
    
    $assignment = Assignment::find(1);
    if (!$assignment) {
        echo "Error: Assignment not found\n";
        exit(1);
    }
    
    echo "  Assignment ID: {$assignment->id}\n";
    echo "  Assessment ID: {$assignment->assessment_id}\n";
    
    $mailer = new Mailer();
    $mailer->send_assignment($user, $assignment->id);
    
    echo "  ✓ Assignment email sent successfully!\n";
    echo "  Check {$user->email} for the email.\n\n";
    
} catch (Exception $e) {
    echo "  ✗ Error: " . $e->getMessage() . "\n";
    echo "  Trace: " . $e->getTraceAsString() . "\n\n";
}

// Test 2: Send test assignments email (multiple assignments in one email)
echo "Test 2: Sending assignments email (multiple)...\n";

try {
    $user = User::find(2); // User Apone
    $assignmentIds = [1];
    // The send_assignments method expects date in 'D, d M Y' format (e.g., "Mon, 19 Oct 2025")
    $expiration = \Carbon\Carbon::now()->addDays(7)->format('D, d M Y');
    $subject = "Test Email: Multiple Assessments Assigned";
    $body = "Hello {name},\n\nThis is a test email from the staging environment.\n\nYour username is: {username}\nYour password is: {password}\n\nPlease visit {login-link} to access your assignments.\n\nThey expire on {expiration-date}.\n\nThank you!";
    
    echo "  User: {$user->name} ({$user->email})\n";
    echo "  Assignment IDs: " . implode(', ', $assignmentIds) . "\n";
    echo "  Expiration: {$expiration}\n";
    
    $mailer = new Mailer();
    $mailer->send_assignments($user, $assignmentIds, $expiration, $subject, $body);
    
    echo "  ✓ Assignments email sent successfully!\n";
    echo "  Check {$user->email} for the email.\n\n";
    
} catch (Exception $e) {
    echo "  ✗ Error: " . $e->getMessage() . "\n";
    echo "  Trace: " . $e->getTraceAsString() . "\n\n";
}

echo "=== Email Testing Complete ===\n";
echo "\nNotes:\n";
echo "- Emails are sent using SES on staging\n";
echo "- From: noreply@cyberworldbuilders.dev\n";
echo "- All emails are BCC'd to xaonst@gmail.com\n";
echo "- Check spam folder if emails don't appear in inbox\n";

