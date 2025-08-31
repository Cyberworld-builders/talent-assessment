<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

echo "Starting SES email test...\n";

try {
    // Display current mail configuration
    echo "Mail driver: " . Config::get('mail.default') . "\n";
    echo "Mail from address: " . Config::get('mail.from.address') . "\n";
    echo "Mail from name: " . Config::get('mail.from.name') . "\n";
    echo "SES region: " . Config::get('services.ses.region') . "\n";
    
    // Test sending a simple email
    echo "Attempting to send test email...\n";
    
    Mail::raw('This is a test email from the staging environment to verify SES configuration is working properly.', function($message) {
        $message->to('admin-goreman@cyberworldbuilders.com')
                ->subject('SES Test - Staging Environment')
                ->from(Config::get('mail.from.address'), Config::get('mail.from.name'));
    });
    
    echo "SUCCESS: Test email sent successfully!\n";
    echo "SES configuration is working properly.\n";
    
} catch (Exception $e) {
    echo "ERROR: Failed to send test email\n";
    echo "Error message: " . $e->getMessage() . "\n";
    echo "Error code: " . $e->getCode() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

echo "SES email test completed successfully!\n";
