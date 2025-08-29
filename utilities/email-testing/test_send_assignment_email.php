<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\User;
use App\Assignment;
use App\Mailer;
use Carbon\Carbon;

echo "🧪 Testing Assignment Email Sending...\n\n";

// Find the user you assigned to (replace with your actual email)
$userEmail = 'jay@cyberworldbuilders.com'; // Change this to your actual email
$user = User::where('email', $userEmail)->first();

if (!$user) {
    echo "❌ User not found. Please update the email address in this script.\n";
    exit(1);
}

echo "✅ Found user: " . $user->name . " (" . $user->email . ")\n";

// Find any assignments for this user (not just recent ones)
$assignments = Assignment::where('user_id', $user->id)->get();

if ($assignments->isEmpty()) {
    echo "❌ No recent assignments found for this user.\n";
    exit(1);
}

echo "✅ Found " . $assignments->count() . " recent assignment(s)\n";

// Get assignment IDs
$assignmentIds = $assignments->pluck('id')->toArray();
$expiration = $assignments->first()->expires->format('D, d M Y');

echo "📧 Sending assignment email...\n";

try {
    $mailer = new Mailer();
    
    $subject = 'New assessments have been assigned to you';
    $body = 'Hello {name}, you have been assigned {assessments}. Please complete by {expiration-date}.';
    
    $mailer->send_assignments($user, $assignmentIds, $expiration, $subject, $body);
    
    echo "✅ Assignment email sent successfully!\n";
    echo "📬 Check your Mailtrap inbox: https://mailtrap.io/inboxes\n";
    
} catch (Exception $e) {
    echo "❌ Error sending email: " . $e->getMessage() . "\n";
}

echo "\n🎉 Test completed!\n";