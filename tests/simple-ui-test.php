<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/**
 * Simple UI Test for CSRF Error Handling
 * This test simulates the CSRF token mismatch scenario
 */

class SimpleUITest extends TestCase
{
    use WithoutMiddleware, DatabaseMigrations, DatabaseTransactions;

    /**
     * Test CSRF token mismatch redirects to login with error message
     */
    public function testCSRFTokenMismatchRedirectsToLogin()
    {
        // Clear session to simulate fresh state
        $this->app['session']->flush();
        
        // Simulate a POST request with invalid CSRF token
        $response = $this->call('POST', '/login', [
            'username' => 'admin@example.com',
            'password' => 'password',
            '_token' => 'invalid-token'
        ]);

        // Should redirect to login page (302)
        $this->assertEquals(302, $response->status());
        $this->assertRedirectedTo('/login');
        
        // Check that error message is in session
        $this->assertSessionHas('errors');
        $errors = session('errors');
        $this->assertTrue($errors->has('_token'));
        $this->assertContains('Your session has expired. Please log in again.', $errors->get('_token'));
    }

    /**
     * Test that session is properly invalidated on CSRF mismatch
     */
    public function testCSRFTokenMismatchInvalidatesSession()
    {
        // First, simulate a logged-in user
        $user = factory(App\User::class)->create();
        $this->actingAs($user);
        
        // Verify user is authenticated
        $this->assertTrue(Auth::check());
        
        // Simulate CSRF token mismatch
        $response = $this->call('POST', '/dashboard/users', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            '_token' => 'invalid-token'
        ]);

        // Should redirect to login
        $this->assertEquals(302, $response->status());
        $this->assertRedirectedTo('/login');
        
        // Session should be invalidated
        $this->assertFalse(Auth::check());
    }

    /**
     * Test that input is preserved on CSRF error
     */
    public function testCSRFTokenMismatchPreservesInput()
    {
        $response = $this->call('POST', '/login', [
            'username' => 'test@example.com',
            'password' => 'password',
            '_token' => 'invalid-token'
        ]);

        // Should preserve input (except _token and password)
        $this->assertSessionHas('_old_input');
        $oldInput = session('_old_input');
        $this->assertEquals('test@example.com', $oldInput['username']);
        $this->assertArrayNotHasKey('_token', $oldInput);
        $this->assertArrayNotHasKey('password', $oldInput);
    }

    /**
     * Test that CSRF error doesn't show 500 error
     */
    public function testCSRFTokenMismatchDoesNotShow500Error()
    {
        // Monitor for 500 errors
        $this->app['log']->shouldReceive('error')->never();
        
        $response = $this->call('POST', '/login', [
            'username' => 'admin@example.com',
            'password' => 'password',
            '_token' => 'invalid-token'
        ]);

        // Should not be a 500 error
        $this->assertNotEquals(500, $response->status());
        $this->assertEquals(302, $response->status());
    }
}
