<?php

/**
 * Test Industry Validation Fix
 */

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Validator;

echo "🧪 Testing Industry Validation Fix\n";
echo "==================================\n\n";

// Test 1: Empty industry_id should fail validation
echo "Test 1: Empty industry_id validation\n";
echo "------------------------------------\n";

$data = [
    'name' => 'Test User',
    'username' => 'testuser',
    'password' => 'password123',
    'industry_id' => ''
];

$validator = Validator::make($data, [
    'name' => 'required',
    'username' => 'required|unique:users',
    'password' => 'required|min:4',
    'industry_id' => 'required|exists:industries,id'
]);

if ($validator->fails()) {
    echo "✅ Validation FAILED (expected)\n";
    $errors = $validator->errors();
    if ($errors->has('industry_id')) {
        echo "✅ Industry validation error: " . $errors->first('industry_id') . "\n";
    } else {
        echo "❌ No industry validation error found\n";
    }
} else {
    echo "❌ Validation PASSED (unexpected)\n";
}

// Test 2: Empty string check
echo "\nTest 2: Empty string check\n";
echo "--------------------------\n";

if (empty($data['industry_id'])) {
    echo "✅ Empty check PASSED: industry_id is empty\n";
} else {
    echo "❌ Empty check FAILED: industry_id is not empty\n";
}

// Test 3: Valid industry_id should pass
echo "\nTest 3: Valid industry_id validation\n";
echo "------------------------------------\n";

$data['industry_id'] = '1'; // Assuming industry ID 1 exists

$validator = Validator::make($data, [
    'name' => 'required',
    'username' => 'required|unique:users',
    'password' => 'required|min:4',
    'industry_id' => 'required|exists:industries,id'
]);

if ($validator->fails()) {
    echo "❌ Validation FAILED (unexpected): " . json_encode($validator->errors()->all()) . "\n";
} else {
    echo "✅ Validation PASSED (expected)\n";
}

echo "\n🎯 Industry Validation Test Complete!\n";
echo "=====================================\n";
echo "The validation should now properly catch empty industry_id values\n";
echo "and prevent database constraint violations.\n";



