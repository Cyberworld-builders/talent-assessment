<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Config;

echo "=== SES Configuration Test ===\n";

try {
    // Display current mail configuration
    echo "Mail driver: " . Config::get('mail.default') . "\n";
    echo "Mail from address: " . Config::get('mail.from.address') . "\n";
    echo "Mail from name: " . Config::get('mail.from.name') . "\n";
    echo "SES region: " . Config::get('services.ses.region') . "\n";
    echo "AWS region: " . Config::get('services.ses.region') . "\n";
    
    // Check environment variables
    echo "\nEnvironment Variables:\n";
    echo "MAIL_DRIVER: " . getenv('MAIL_DRIVER') . "\n";
    echo "MAIL_FROM_ADDRESS: " . getenv('MAIL_FROM_ADDRESS') . "\n";
    echo "MAIL_FROM_NAME: " . getenv('MAIL_FROM_NAME') . "\n";
    echo "AWS_DEFAULT_REGION: " . getenv('AWS_DEFAULT_REGION') . "\n";
    
    echo "\nConfiguration test completed successfully!\n";
    
} catch (Exception $e) {
    echo "ERROR: Configuration test failed\n";
    echo "Error message: " . $e->getMessage() . "\n";
    echo "Error code: " . $e->getCode() . "\n";
    exit(1);
}
