<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;

class BenchmarksControllerTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test that the template download route works correctly.
     * This ensures that /dashboard/benchmarks/{assessmentId}/template
     * doesn't get matched by the more general /dashboard/benchmarks/{assessmentId}/{industryId} route.
     */
    public function testDownloadTemplateRoute()
    {
        // Create a test user first
        $user = factory(App\User::class)->create();

        // Create a test assessment
        $assessment = factory(App\Assessment::class)->create([
            'user_id' => $user->id,
            'name' => 'Test Assessment',
            'logo' => 'test-logo.png',
            'background' => 'test-bg.png',
            'paginate' => 10,
            'items_per_page' => 5,
            'timed' => false,
            'use_custom_fields' => false,
            'target' => 100,
            'last_modified' => \Carbon\Carbon::now()
        ]);

        // Create some dimensions for the assessment
        $dimension1 = factory(App\Dimension::class)->create([
            'assessment_id' => $assessment->id,
            'name' => 'Test Dimension 1',
            'parent' => 0,
            'code' => 'TD1'
        ]);

        $dimension2 = factory(App\Dimension::class)->create([
            'assessment_id' => $assessment->id,
            'name' => 'Test Dimension 2',
            'parent' => 0,
            'code' => 'TD2'
        ]);

        // Test that the template download route works
        $response = $this->call('GET', "/dashboard/benchmarks/{$assessment->id}/template");

        // Should return a successful response (Excel file download)
        $this->assertEquals(200, $response->getStatusCode());
        
        // Should be an Excel file
        $this->assertContains('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', $response->headers->get('Content-Type'));
        
        // Should have the correct filename
        $this->assertContains('benchmarks_template_Test_Assessment', $response->headers->get('Content-Disposition'));
    }

    /**
     * Test that the template download route requires authentication.
     */
    public function testDownloadTemplateRequiresAuthentication()
    {
        // Create a test user first
        $user = factory(App\User::class)->create();

        $assessment = factory(App\Assessment::class)->create([
            'user_id' => $user->id,
            'name' => 'Test Assessment',
            'logo' => 'test-logo.png',
            'background' => 'test-bg.png',
            'paginate' => 10,
            'items_per_page' => 5,
            'timed' => false,
            'use_custom_fields' => false,
            'target' => 100,
            'last_modified' => \Carbon\Carbon::now()
        ]);

        // Test without authentication
        $response = $this->call('GET', "/dashboard/benchmarks/{$assessment->id}/template");

        // Should redirect to login
        $this->assertEquals(302, $response->getStatusCode());
    }

    /**
     * Test that the template download route returns 404 for non-existent assessment.
     */
    public function testDownloadTemplateForNonExistentAssessment()
    {
        $response = $this->call('GET', "/dashboard/benchmarks/99999/template");

        // Should return 404
        $this->assertEquals(404, $response->getStatusCode());
    }

    /**
     * Test that the industry route still works correctly.
     * This ensures our route reordering didn't break the existing functionality.
     */
    public function testIndustryRouteStillWorks()
    {
        // Create a test user first
        $user = factory(App\User::class)->create();

        $assessment = factory(App\Assessment::class)->create([
            'user_id' => $user->id,
            'name' => 'Test Assessment',
            'logo' => 'test-logo.png',
            'background' => 'test-bg.png',
            'paginate' => 10,
            'items_per_page' => 5,
            'timed' => false,
            'use_custom_fields' => false,
            'target' => 100,
            'last_modified' => \Carbon\Carbon::now()
        ]);

        $industry = factory(App\Industry::class)->create([
            'name' => 'Test Industry'
        ]);

        // Test that the industry route works
        $response = $this->call('GET', "/dashboard/benchmarks/{$assessment->id}/{$industry->id}");

        // Should redirect to login (since we're not authenticated)
        $this->assertEquals(302, $response->getStatusCode());
    }
}
