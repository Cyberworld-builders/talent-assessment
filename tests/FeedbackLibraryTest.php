<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\FeedbackLibrary;
use App\Client;

class FeedbackLibraryTest extends TestCase
{
    use DatabaseTransactions;

    public function test_can_create_feedback_library()
    {
        $feedbackData = [
            'dimensions' => [
                'leadership' => [
                    'high' => 'Excellent leadership skills',
                    'medium' => 'Good leadership potential',
                    'low' => 'Leadership development needed'
                ]
            ]
        ];

        $library = new FeedbackLibrary([
            'name' => 'Test Library',
            'feedback' => $feedbackData
        ]);

        $this->assertEquals('Test Library', $library->name);
        $this->assertTrue(is_array($library->feedback));
        $this->assertArrayHasKey('dimensions', $library->feedback);
    }

    public function test_json_encoding_and_decoding()
    {
        $feedbackData = [
            'dimensions' => [
                'communication' => [
                    'high' => 'Outstanding communication skills',
                    'medium' => 'Good communication abilities',
                    'low' => 'Communication skills need improvement'
                ]
            ]
        ];

        $library = new FeedbackLibrary([
            'name' => 'JSON Test Library',
            'feedback' => $feedbackData
        ]);

        // Test that feedback is properly encoded when saved
        $library->save();

        // Retrieve from database and test decoding
        $retrievedLibrary = FeedbackLibrary::find($library->id);
        $this->assertEquals($feedbackData, $retrievedLibrary->feedback);
    }

    public function test_client_relationship()
    {
        // Create a client
        $client = Client::create([
            'name' => 'Test Client',
            'address' => 'Test Address'
        ]);

        // Create feedback library with client relationship
        $library = FeedbackLibrary::create([
            'name' => 'Client Library',
            'feedback' => ['test' => 'value'],
            'client_id' => $client->id
        ]);

        // Test relationship
        $this->assertEquals($client->id, $library->client->id);
        $this->assertEquals($library->id, $client->feedbackLibraries->first()->id);
    }

    public function test_unique_name_validation()
    {
        // Create first library
        FeedbackLibrary::create([
            'name' => 'Unique Test Library',
            'feedback' => ['test' => 'value1']
        ]);

        // Try to create second library with same name - should fail
        $this->setExpectedException(\Illuminate\Database\QueryException::class);
        
        FeedbackLibrary::create([
            'name' => 'Unique Test Library',
            'feedback' => ['test' => 'value2']
        ]);
    }
}
