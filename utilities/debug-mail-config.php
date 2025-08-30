<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Config;

echo "=== Mail Configuration Debug ===\n";

try {
    // Check environment variables directly
    echo "Environment Variables:\n";
    echo "MAIL_DRIVER: " . getenv('MAIL_DRIVER') . "\n";
    echo "MAIL_FROM_ADDRESS: " . getenv('MAIL_FROM_ADDRESS') . "\n";
    echo "MAIL_FROM_NAME: " . getenv('MAIL_FROM_NAME') . "\n";
    echo "AWS_DEFAULT_REGION: " . getenv('AWS_DEFAULT_REGION') . "\n";
    
    // Check Laravel config
    echo "\nLaravel Config:\n";
    echo "mail.driver: " . Config::get('mail.driver') . "\n";
    echo "mail.from.address: " . Config::get('mail.from.address') . "\n";
    echo "mail.from.name: " . Config::get('mail.from.name') . "\n";
    
    // Check if config is cached
    echo "\nConfig Cache Status:\n";
    $configPath = storage_path('framework/cache/config.php');
    if (file_exists($configPath)) {
        echo "Config cache exists at: $configPath\n";
        echo "Config cache size: " . filesize($configPath) . " bytes\n";
        echo "Config cache modified: " . date('Y-m-d H:i:s', filemtime($configPath)) . "\n";
    } else {
        echo "No config cache found\n";
    }
    
    // Check .env file
    echo "\n.env file check:\n";
    $envPath = base_path('.env');
    if (file_exists($envPath)) {
        echo ".env file exists at: $envPath\n";
        echo ".env file size: " . filesize($envPath) . " bytes\n";
        echo ".env file modified: " . date('Y-m-d H:i:s', filemtime($envPath)) . "\n";
        
        // Check specific lines
        $envContent = file_get_contents($envPath);
        if (strpos($envContent, 'MAIL_DRIVER=ses') !== false) {
            echo "MAIL_DRIVER=ses found in .env\n";
        } else {
            echo "MAIL_DRIVER=ses NOT found in .env\n";
        }
    } else {
        echo ".env file not found\n";
    }
    
    // Check .env.dev file
    echo "\n.env.dev file check:\n";
    $envDevPath = base_path('.env.dev');
    if (file_exists($envDevPath)) {
        echo ".env.dev file exists at: $envDevPath\n";
        echo ".env.dev file size: " . filesize($envDevPath) . " bytes\n";
        echo ".env.dev file modified: " . date('Y-m-d H:i:s', filemtime($envDevPath)) . "\n";
        
        // Check specific lines
        $envDevContent = file_get_contents($envDevPath);
        if (strpos($envDevContent, 'MAIL_DRIVER=ses') !== false) {
            echo "MAIL_DRIVER=ses found in .env.dev\n";
        } else {
            echo "MAIL_DRIVER=ses NOT found in .env.dev\n";
        }
    } else {
        echo ".env.dev file not found\n";
    }
    
    echo "\nDebug completed successfully!\n";
    
} catch (Exception $e) {
    echo "ERROR: Debug failed\n";
    echo "Error message: " . $e->getMessage() . "\n";
    echo "Error code: " . $e->getCode() . "\n";
    exit(1);
}
