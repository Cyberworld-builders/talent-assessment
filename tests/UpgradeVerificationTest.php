<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Assessment;
use App\Question;
use App\Dimension;
use App\User;
use App\Client;
use App\Language;

class UpgradeVerificationTest extends TestCase
{
    /**
     * Test that basic Laravel functionality works after upgrade
     */
    public function testLaravelBasicFunctionality()
    {
        // Test that Laravel is working
        $this->assertNotNull(app());
        $this->assertInstanceOf('Illuminate\Foundation\Application', app());
        
        // Test that database connection works
        $this->assertInstanceOf('Illuminate\Database\Connection', \DB::connection());
        
        // Test that config system is working
        $this->assertNotNull(config());
    }

    /**
     * Test that models can be instantiated
     */
    public function testModelsCanBeInstantiated()
    {
        // Test core models can be created
        $user = new User();
        $this->assertInstanceOf(User::class, $user);
        
        $assessment = new Assessment();
        $this->assertInstanceOf(Assessment::class, $assessment);
        
        $question = new Question();
        $this->assertInstanceOf(Question::class, $question);
        
        $dimension = new Dimension();
        $this->assertInstanceOf(Dimension::class, $dimension);
        
        $client = new Client();
        $this->assertInstanceOf(Client::class, $client);
        
        $language = new Language();
        $this->assertInstanceOf(Language::class, $language);
    }

    /**
     * Test that spatie/laravel-permission is working
     */
    public function testSpatiePermissionPackage()
    {
        // Test that the Role model exists and can be instantiated
        $role = new \Spatie\Permission\Models\Role();
        $this->assertInstanceOf(\Spatie\Permission\Models\Role::class, $role);
        
        // Test that the Permission model exists and can be instantiated
        $permission = new \Spatie\Permission\Models\Permission();
        $this->assertInstanceOf(\Spatie\Permission\Models\Permission::class, $permission);
    }

    /**
     * Test that User model has the HasRoles trait
     */
    public function testUserModelHasRolesTrait()
    {
        $user = new User();
        
        // Test that the HasRoles trait methods are available
        $this->assertTrue(method_exists($user, 'assignRole'));
        $this->assertTrue(method_exists($user, 'hasRole'));
        $this->assertTrue(method_exists($user, 'roles'));
    }

    /**
     * Test that PHP 8.1 features are working
     */
    public function testPhp81Features()
    {
        // Test that we're running PHP 8.1+
        $this->assertGreaterThanOrEqual(80100, PHP_VERSION_ID);
        
        // Test enum support (PHP 8.1 feature)
        $this->assertTrue(function_exists('enum_exists'));
        
        // Test that nullable types work
        $testFunction = function(?string $value = null): ?string {
            return $value;
        };
        
        $this->assertNull($testFunction(null));
        $this->assertEquals('test', $testFunction('test'));
    }

    /**
     * Test that Laravel 9 features are working
     */
    public function testLaravel9Features()
    {
        // Test that we're running Laravel 9
        $version = app()->version();
        $this->assertStringStartsWith('9.', $version);
        
        // Test that the new helper functions exist
        $this->assertTrue(function_exists('str'));
        $this->assertTrue(function_exists('collect'));
        
        // Test str helper
        $result = str('hello world')->title();
        $this->assertEquals('Hello World', $result);
    }
}
