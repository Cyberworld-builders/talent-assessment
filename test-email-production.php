<?php

/**
 * Test Email Script for Production
 * 
 * This script sends a test email to verify the email system is working correctly on production.
 * Usage: docker exec talent-assessment-app-production php test-email-production.php
 * 
 * WARNING: Only sends to test accounts (cyberworldbuilders.com domain) to avoid spamming real users.
 */

// Bootstrap Laravel
require __DIR__.'/bootstrap/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\User;
use App\Assignment;
use App\Mailer;

echo "=== Email Test Script for Production ===\n\n";

// Use User Apone (forwarded email)
$user = User::find(2); // User Apone
if (!$user) {
    echo "Error: User not found\n";
    exit(1);
}

echo "Testing with user: {$user->name} ({$user->email})\n\n";

// Test 1: Send assignment email
echo "Test 1: Sending assignment email...\n";

try {
    echo "  User: {$user->name} ({$user->email})\n";
    
    // Find any existing assignment to use as reference
    $anyAssignment = Assignment::first();
    if (!$anyAssignment) {
        echo "  ✗ Error: No assignments found in database.\n\n";
    } else {
        echo "  Using assignment ID: {$anyAssignment->id}\n";
        echo "  Assessment ID: {$anyAssignment->assessment_id}\n";
        
        $mailer = new Mailer();
        $mailer->send_assignment($user, $anyAssignment->id);
        
        echo "  ✓ Assignment email sent successfully!\n";
        echo "  Check {$user->email} for the email.\n";
        echo "  Note: Production is using Mailtrap, so check the Mailtrap inbox.\n\n";
    }
    
} catch (Exception $e) {
    echo "  ✗ Error: " . $e->getMessage() . "\n";
    echo "  Trace: " . $e->getTraceAsString() . "\n\n";
}

// Test 2: Send test assignments email (multiple assignments in one email)
echo "Test 2: Sending assignments email (multiple)...\n";

try {
    // Get any existing assignments to use as reference
    $assignments = Assignment::take(2)->get()->pluck('id')->toArray();
    
    if (empty($assignments)) {
        echo "  ✗ Skipping - no existing assignments found in database.\n\n";
    } else {
        // The send_assignments method expects date in 'D, d M Y' format (e.g., "Mon, 19 Oct 2025")
        $expiration = \Carbon\Carbon::now()->addDays(7)->format('D, d M Y');
        $subject = "Test Email: Multiple Assessments Assigned (Production)";
        $body = "Hello {name},\n\nThis is a test email from the production environment.\n\nYour username is: {username}\nYour password is: {password}\n\nPlease visit {login-link} to access your assignments.\n\nThey expire on {expiration-date}.\n\nThank you!";
        
        echo "  User: {$user->name} ({$user->email})\n";
        echo "  Assignment IDs: " . implode(', ', $assignments) . "\n";
        echo "  Expiration: {$expiration}\n";
        
        $mailer = new Mailer();
        $mailer->send_assignments($user, $assignments, $expiration, $subject, $body);
        
        echo "  ✓ Assignments email sent successfully!\n";
        echo "  Check {$user->email} for the email.\n";
        echo "  Note: Production is using Mailtrap, so check the Mailtrap inbox.\n\n";
    }
    
} catch (Exception $e) {
    echo "  ✗ Error: " . $e->getMessage() . "\n";
    echo "  Trace: " . $e->getTraceAsString() . "\n\n";
}

echo "=== Email Testing Complete ===\n";
echo "\nNotes:\n";
echo "- Production is currently using Mailtrap (SMTP testing service)\n";
echo "- Emails will NOT be delivered to real inboxes\n";
echo "- Check Mailtrap inbox at https://mailtrap.io to view sent emails\n";
echo "- To send real emails, update MAIL_DRIVER to 'ses' in production environment\n";
echo "- All emails are BCC'd to xaonst@gmail.com\n";

