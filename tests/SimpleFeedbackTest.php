<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\FeedbackLibrary;
use App\Client;
use App\User;
use App\Language;
use Bican\Roles\Models\Role;

class SimpleFeedbackTest extends TestCase
{
    protected $user;
    protected $client;
    protected $language;

    public function setUp()
    {
        parent::setUp();
        
        // Disable CSRF protection for tests
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);
        
        // Create test data
        $this->language = Language::firstOrCreate([
            'name' => 'English',
            'native_name' => 'English',
            'code' => 'en'
        ]);
        
        $this->client = Client::firstOrCreate([
            'name' => 'Simple Test Client',
            'require_profile' => true,
            'require_research' => true
        ]);
        
        // Ensure roles exist for testing
        $this->createRolesIfNeeded();
        
        // Create test user
        $this->user = User::create([
            'username' => 'simpleuser_' . uniqid(),
            'name' => 'Simple Test User',
            'email' => 'simpleuser_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'client_id' => $this->client->id,
            'language_id' => $this->language->id,
            'completed_profile' => true,
            'completed_research' => true
        ]);
        
        $adminRole = Role::where('slug', 'admin')->first();
        $this->user->attachRole($adminRole);
    }
    
    /**
     * Create roles if they don't exist (for CI environment)
     */
    private function createRolesIfNeeded()
    {
        $roles = [
            ['name' => 'AOE Admin', 'slug' => 'admin', 'level' => 4],
            ['name' => 'Reseller', 'slug' => 'reseller', 'level' => 3],
            ['name' => 'Client Admin', 'slug' => 'client', 'level' => 2],
            ['name' => 'User', 'slug' => 'user', 'level' => 1]
        ];
        
        foreach ($roles as $roleData) {
            Role::firstOrCreate(['slug' => $roleData['slug']], $roleData);
        }
    }

    /**
     * Test basic feedback library creation
     */
    public function testBasicFeedbackLibraryCreation()
    {
        $feedbackData = [
            'library_type' => 'involved-360',
            'dimensions' => [
                'creative-problem-solving' => [
                    'high' => 'High feedback',
                    'medium' => 'Medium feedback',
                    'low' => 'Low feedback'
                ]
            ]
        ];

        $library = FeedbackLibrary::create([
            'name' => 'Basic Test Library',
            'feedback' => $feedbackData
        ]);

        $this->assertNotNull($library->id);
        $this->assertEquals('Basic Test Library', $library->name);
        $this->assertArrayHasKey('dimensions', $library->feedback);
    }

    /**
     * Test feedback index page loads
     */
    public function testFeedbackIndexPage()
    {
        $this->be($this->user);

        $response = $this->call('GET', 'dashboard/feedback');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('Feedback Libraries', $response->getContent());
    }

    /**
     * Test simple save functionality
     */
    public function testSimpleSave()
    {
        $this->be($this->user);

        $data = [
            'library_type' => 'involved-360',
            'name' => 'Simple Test Library',
            'dimensions' => [
                'test-dimension' => [
                    'high' => 'High test feedback',
                    'medium' => 'Medium test feedback',
                    'low' => 'Low test feedback'
                ]
            ]
        ];

        $response = $this->call('POST', 'dashboard/feedback/save', $data);

        // Debug the response
        if ($response->getStatusCode() !== 200) {
            echo "Response Status: " . $response->getStatusCode() . "\n";
            echo "Response Content: " . $response->getContent() . "\n";
        }

        $this->assertEquals(200, $response->getStatusCode());
        
        $responseData = json_decode($response->getContent(), true);
        $this->assertTrue($responseData['success']);
    }
}
