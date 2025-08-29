<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\User;
use App\Assessment;
use App\Assignment;
use App\Mailer;
use Carbon\Carbon;

echo "🔧 Creating Assignment Manually...\n\n";

// Find the user
$user = User::where('email', 'jay@cyberworldbuilders.com')->first();

if (!$user) {
    echo "❌ User not found.\n";
    exit(1);
}

echo "✅ Found user: " . $user->name . " (" . $user->email . ")\n";

// Find an assessment to assign
$assessment = Assessment::first();

if (!$assessment) {
    echo "❌ No assessments found.\n";
    exit(1);
}

echo "✅ Found assessment: " . $assessment->name . "\n";

// Create assignment
$expiration = Carbon::now()->addDays(7);
$assignment = new Assignment();
$assignment->user_id = $user->id;
$assignment->assessment_id = $assessment->id;
$assignment->expires = $expiration;
$assignment->save();

echo "✅ Created assignment ID: " . $assignment->id . "\n";

// Send email
echo "📧 Sending assignment email...\n";

try {
    $mailer = new Mailer();
    
    $subject = 'New assessments have been assigned to you';
    $body = 'Hello {name}, you have been assigned {assessments}. Please complete by {expiration-date}.';
    
    $mailer->send_assignments($user, [$assignment->id], $expiration->format('D, d M Y'), $subject, $body);
    
    echo "✅ Assignment email sent successfully!\n";
    echo "📬 Check your Mailtrap inbox: https://mailtrap.io/inboxes\n";
    
} catch (Exception $e) {
    echo "❌ Error sending email: " . $e->getMessage() . "\n";
}

echo "\n🎉 Assignment created and email sent!\n";
