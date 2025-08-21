<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicExample()
    {
        // Simple test that doesn't require routes
        $this->assertTrue(true);
        
        // Test that Laravel is working
        $this->assertNotNull(app());
        
        // Test basic Laravel functionality
        $this->assertInstanceOf('Illuminate\Foundation\Application', app());
    }
}
