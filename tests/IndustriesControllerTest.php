<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Industry;

class IndustriesControllerTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test industries index page requires authentication.
     *
     * @return void
     */
    public function testIndustriesIndexRequiresAuthentication()
    {
        $response = $this->call('GET', '/dashboard/industries');
        
        // Should redirect to login
        $this->assertEquals(302, $response->getStatusCode());
    }

    /**
     * Test industries create page requires authentication.
     *
     * @return void
     */
    public function testIndustriesCreateRequiresAuthentication()
    {
        $response = $this->call('GET', '/dashboard/industries/create');
        
        // Should redirect to login
        $this->assertEquals(302, $response->getStatusCode());
    }
}
