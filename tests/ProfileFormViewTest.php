<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\User;
use App\Industry;
use App\Client;

class ProfileFormViewTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;
    protected $client;

    public function setUp(): void
    {
        parent::setUp();
        
        // Ensure we have a language record
        $language = \App\Language::first();
        if (!$language) {
            $language = \App\Language::create([
                'name' => 'English',
                'native_name' => 'English',
                'code' => 'en'
            ]);
        }
        
        // Create test client with unique name
        $this->client = Client::create([
            'name' => 'Test Client ' . uniqid(),
            'require_profile' => true,
            'require_research' => true
        ]);

        // Create test user with unique email
        $this->user = User::create([
            'username' => 'testuser_' . uniqid(),
            'name' => 'John Doe',
            'email' => 'john_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'client_id' => $this->client->id,
            'language_id' => $language->id, // Use the actual language ID
            'completed_profile' => false,
            'completed_research' => false
        ]);

        // Ensure the user is properly saved with language_id
        $this->user = User::find($this->user->id);
    }

    /**
     * Test that profile form renders without errors when industries exist
     */
    public function testProfileFormRendersWithIndustries()
    {
        // Create industries with unique names
        Industry::create(['name' => 'Test Technology Industry ' . uniqid()]);
        Industry::create(['name' => 'Test Healthcare Industry ' . uniqid()]);
        Industry::create(['name' => 'Test Finance Industry ' . uniqid()]);

        $this->actingAs($this->user);

        // Debug: Check user state
        echo "User language_id: " . $this->user->language_id . "\n";
        echo "User completed_profile: " . ($this->user->completed_profile ? 'true' : 'false') . "\n";
        echo "User completed_research: " . ($this->user->completed_research ? 'true' : 'false') . "\n";
        echo "User client_id: " . $this->user->client_id . "\n";

        $response = $this->call('GET', '/profile');

        if ($response->getStatusCode() == 302) {
            echo "Redirecting to: " . $response->getTargetUrl() . "\n";
        }

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('Create Your Profile', $response->getContent());
        $this->assertContains('Select Industry', $response->getContent());
        $this->assertContains('name="industry_id"', $response->getContent());
    }

    /**
     * Test that profile form renders without errors when no industries exist
     */
    public function testProfileFormRendersWithoutIndustries()
    {
        // Ensure no industries exist (but don't truncate due to foreign key constraints)
        Industry::where('name', 'like', 'Test%')->delete();

        $this->actingAs($this->user);

        $response = $this->call('GET', '/profile');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('Create Your Profile', $response->getContent());
        $this->assertContains('Select Industry', $response->getContent());
        $this->assertContains('name="industry_id"', $response->getContent());
        
        // Should not contain any test industry names
        $this->assertNotContains('Test Technology Industry', $response->getContent());
    }

    /**
     * Test that profile form handles user with existing industry selection
     */
    public function testProfileFormWithExistingIndustrySelection()
    {
        $industry = Industry::create(['name' => 'Test Technology Industry ' . uniqid()]);
        
        $this->user->industry_id = $industry->id;
        $this->user->save();

        $this->actingAs($this->user);

        $response = $this->call('GET', '/profile');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains($industry->name, $response->getContent());
        $this->assertContains('selected', $response->getContent()); // Should have selected option
    }

    /**
     * Test that profile form handles user with null industry selection
     */
    public function testProfileFormWithNullIndustrySelection()
    {
        $industry = Industry::create(['name' => 'Test Technology Industry ' . uniqid()]);
        
        $this->user->industry_id = null;
        $this->user->save();

        $this->actingAs($this->user);

        $response = $this->call('GET', '/profile');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains($industry->name, $response->getContent());
        $this->assertContains('Select Industry', $response->getContent());
    }

    /**
     * Test that profile form includes all required form fields
     */
    public function testProfileFormIncludesAllRequiredFields()
    {
        Industry::create(['name' => 'Test Technology Industry ' . uniqid()]);

        $this->actingAs($this->user);

        $response = $this->call('GET', '/profile');

        $this->assertEquals(200, $response->getStatusCode());
        
        // Check for all required form fields
        $this->assertContains('name="first_name"', $response->getContent());
        $this->assertContains('name="middle_name"', $response->getContent());
        $this->assertContains('name="last_name"', $response->getContent());
        $this->assertContains('name="email"', $response->getContent());
        $this->assertContains('name="password"', $response->getContent());
        $this->assertContains('name="password_confirmation"', $response->getContent());
        $this->assertContains('name="industry_id"', $response->getContent());
        
        // Check for form submission
        $this->assertContains('method="POST"', $response->getContent());
        $this->assertContains('action="https://localhost/profile"', $response->getContent());
    }

    /**
     * Test that profile form handles special characters in industry names
     */
    public function testProfileFormHandlesSpecialCharactersInIndustryNames()
    {
        $industry1 = Industry::create(['name' => 'Test Technology & IT ' . uniqid()]);
        $industry2 = Industry::create(['name' => 'Test Healthcare & Medical ' . uniqid()]);
        $industry3 = Industry::create(['name' => 'Test Finance & Banking ' . uniqid()]);

        $this->actingAs($this->user);

        $response = $this->call('GET', '/profile');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains(htmlspecialchars($industry1->name), $response->getContent());
        $this->assertContains(htmlspecialchars($industry2->name), $response->getContent());
        $this->assertContains(htmlspecialchars($industry3->name), $response->getContent());
    }

    /**
     * Test that profile form handles large number of industries
     */
    public function testProfileFormHandlesLargeNumberOfIndustries()
    {
        // Create many industries
        $industries = [];
        for ($i = 1; $i <= 50; $i++) {
            $industries[] = Industry::create(['name' => "Industry {$i} " . uniqid()]);
        }

        $this->actingAs($this->user);

        $response = $this->call('GET', '/profile');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains($industries[0]->name, $response->getContent());
        $this->assertContains($industries[49]->name, $response->getContent());
        $this->assertContains('Select Industry', $response->getContent());
    }

    /**
     * Test that profile form displays user's current name correctly
     */
    public function testProfileFormDisplaysCurrentUserName()
    {
        Industry::create(['name' => 'Test Technology Industry ' . uniqid()]);

        // Test with single name
        $this->user->name = 'John';
        $this->user->save();

        $this->actingAs($this->user);

        $response = $this->call('GET', '/profile');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('value="John"', $response->getContent());

        // Test with two names
        $this->user->name = 'John Doe';
        $this->user->save();

        $response = $this->call('GET', '/profile');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('value="John"', $response->getContent());
        $this->assertContains('value="Doe"', $response->getContent());

        // Test with three names
        $this->user->name = 'John Marie Doe';
        $this->user->save();

        $response = $this->call('GET', '/profile');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('value="John"', $response->getContent());
        $this->assertContains('value="Marie"', $response->getContent());
        $this->assertContains('value="Doe"', $response->getContent());
    }

    /**
     * Test that profile form handles empty user name gracefully
     */
    public function testProfileFormHandlesEmptyUserName()
    {
        Industry::create(['name' => 'Test Technology Industry ' . uniqid()]);

        $this->user->name = '';
        $this->user->save();

        $this->actingAs($this->user);

        $response = $this->call('GET', '/profile');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('Create Your Profile', $response->getContent());
    }

    /**
     * Test that profile form includes CSRF protection
     */
    public function testProfileFormIncludesCSRFProtection()
    {
        Industry::create(['name' => 'Test Technology Industry ' . uniqid()]);

        $this->actingAs($this->user);

        $response = $this->call('GET', '/profile');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('_token', $response->getContent());
    }
}
