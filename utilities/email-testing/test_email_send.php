<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Set up environment for email sending
$_SERVER['SERVER_NAME'] = 'localhost';
$_SERVER['HTTP_HOST'] = 'localhost';

use App\User;
use App\Mailer;

echo "📧 Testing Email Sending...\n\n";

// Find the user
$user = User::where('email', 'jay@cyberworldbuilders.com')->first();

if (!$user) {
    echo "❌ User not found.\n";
    exit(1);
}

echo "✅ Found user: " . $user->name . " (" . $user->email . ")\n";

// Test simple email sending
try {
    $mailer = new Mailer();
    
    // Test basic email functionality using send_assignment
    $result = $mailer->send_assignment($user, 97); // Use the assignment ID we created
    
    if ($result) {
        echo "✅ Test email sent successfully!\n";
        echo "📬 Check your Mailtrap inbox: https://mailtrap.io/inboxes\n";
    } else {
        echo "❌ Email sending failed.\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error sending email: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\n🎉 Test completed!\n";
