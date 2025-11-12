<?php

/*
 * Test script to send a reminder email
 * Usage: php send-test-reminder.php
 */

require __DIR__.'/bootstrap/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\User;
use App\Assignment;
use App\Mailer;
use Carbon\Carbon;

// Find the user
$email = 'user-apone@cyberworldbuilders.com';
$user = User::where('email', $email)->first();

if (!$user) {
    echo "User not found: $email\n";
    exit(1);
}

echo "Found user: {$user->name} (ID: {$user->id})\n";

// Find an assignment for this user
$assignment = Assignment::where('user_id', $user->id)
    ->whereNotNull('assessment_id')
    ->first();

if (!$assignment) {
    echo "No assignments found for this user\n";
    exit(1);
}

echo "Found assignment: ID {$assignment->id} for assessment: {$assignment->assessment()->name}\n";

// Send the reminder
echo "Sending test reminder email to {$user->email}...\n";

try {
    $mailer = new Mailer();
    
    // Enable debug mode for Mail
    \Config::set('mail.pretend', false);
    
    echo "Mail driver: " . \Config::get('mail.driver') . "\n";
    echo "Mail from: " . \Config::get('mail.from.address') . "\n";
    
    $result = $mailer->send_reminder($assignment);
    
    if ($result) {
        echo "✓ Reminder email sent successfully!\n";
    } else {
        echo "✗ Failed to send reminder email\n";
    }
} catch (\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

