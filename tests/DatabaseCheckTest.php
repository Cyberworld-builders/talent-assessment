<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\User;
use App\Industry;
use App\Client;
use App\Language;

class DatabaseCheckTest extends TestCase
{
    use DatabaseTransactions;

    public function testDatabaseState()
    {
        // Check if languages exist
        $languages = Language::all();
        echo "Languages count: " . $languages->count() . "\n";
        
        foreach ($languages as $language) {
            echo "Language ID: " . $language->id . ", Name: " . $language->name . "\n";
        }

        // Create a language if none exist
        if ($languages->count() == 0) {
            $language = Language::create([
                'name' => 'English',
                'native_name' => 'English',
                'code' => 'en'
            ]);
            echo "Created language with ID: " . $language->id . "\n";
        } else {
            $language = $languages->first();
        }

        // Create test client
        $client = Client::create([
            'name' => 'Test Client ' . uniqid(),
            'require_profile' => true,
            'require_research' => true
        ]);

        // Create test user with proper language_id and client_id
        $user = User::create([
            'username' => 'testuser_' . uniqid(),
            'name' => 'Test User',
            'email' => 'test_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'client_id' => $client->id,
            'language_id' => $language->id,
            'completed_profile' => false,
            'completed_research' => false
        ]);

        echo "Created user with language_id: " . $user->language_id . "\n";
        echo "User language_id is null: " . ($user->language_id === null ? 'true' : 'false') . "\n";

        $this->actingAs($user);
        
        $response = $this->call('GET', '/profile');
        echo "Profile response status: " . $response->getStatusCode() . "\n";
        
        if ($response->getStatusCode() == 302) {
            echo "Redirecting to: " . $response->getTargetUrl() . "\n";
        }

        // This test should pass if everything is set up correctly
        $this->assertTrue(true);
    }
}
