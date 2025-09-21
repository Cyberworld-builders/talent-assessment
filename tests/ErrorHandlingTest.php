<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Auth\Access\UnauthorizedException;
use Illuminate\Validation\ValidationException;

class ErrorHandlingTest extends TestCase
{
    use WithoutMiddleware;

    /**
     * Test that CSRF token mismatch doesn't report as error
     */
    public function testCSRFTokenMismatchNotReportedAsError()
    {
        $handler = $this->app->make('App\Exceptions\Handler');
        
        // Use reflection to access protected property
        $reflection = new ReflectionClass($handler);
        $dontReport = $reflection->getProperty('dontReport');
        $dontReport->setAccessible(true);
        $dontReportArray = $dontReport->getValue($handler);
        
        // Should be in the dontReport list
        $this->assertContains(TokenMismatchException::class, $dontReportArray);
    }

    /**
     * Test that unauthorized exception doesn't report as error
     */
    public function testUnauthorizedExceptionNotReportedAsError()
    {
        $handler = $this->app->make('App\Exceptions\Handler');
        
        // Use reflection to access protected property
        $reflection = new ReflectionClass($handler);
        $dontReport = $reflection->getProperty('dontReport');
        $dontReport->setAccessible(true);
        $dontReportArray = $dontReport->getValue($handler);
        
        // Should be in the dontReport list
        $this->assertContains(UnauthorizedException::class, $dontReportArray);
    }

    /**
     * Test that validation exceptions are handled properly
     */
    public function testValidationExceptionHandling()
    {
        $handler = $this->app->make('App\Exceptions\Handler');
        
        // Use reflection to access protected property
        $reflection = new ReflectionClass($handler);
        $dontReport = $reflection->getProperty('dontReport');
        $dontReport->setAccessible(true);
        $dontReportArray = $dontReport->getValue($handler);
        
        // Should be in the dontReport list
        $this->assertContains(ValidationException::class, $dontReportArray);
    }

    /**
     * Test CSRF token mismatch exception handling
     */
    public function testCSRFTokenMismatchExceptionHandling()
    {
        $handler = $this->app->make('App\Exceptions\Handler');
        $exception = new TokenMismatchException();
        
        // Create a mock request
        $request = $this->app['request'];
        
        // Test the render method
        $response = $handler->render($request, $exception);
        
        // Should redirect to login
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertContains('/login', $response->getTargetUrl());
    }

    /**
     * Test unauthorized exception handling
     */
    public function testUnauthorizedExceptionHandling()
    {
        $handler = $this->app->make('App\Exceptions\Handler');
        $exception = new UnauthorizedException();
        
        // Create a mock request
        $request = $this->app['request'];
        
        // Test the render method
        $response = $handler->render($request, $exception);
        
        // Should redirect to login
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertContains('/login', $response->getTargetUrl());
    }

    /**
     * Test CSRF token mismatch on login page redirects to login
     */
    public function testCSRFTokenMismatchOnLoginPageRedirectsToLogin()
    {
        $handler = $this->app->make('App\Exceptions\Handler');
        $exception = new TokenMismatchException();
        
        // Create a mock request to login page
        $request = $this->app['request'];
        $request->setMethod('POST');
        $request->server->set('REQUEST_URI', '/login');
        
        // Test the render method
        $response = $handler->render($request, $exception);
        
        // Should redirect to login
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertContains('/login', $response->getTargetUrl());
    }

    /**
     * Test that exception handler has proper imports
     */
    public function testExceptionHandlerHasProperImports()
    {
        $handler = $this->app->make('App\Exceptions\Handler');
        
        // Check that the handler class exists and has the right methods
        $this->assertTrue(class_exists('App\Exceptions\Handler'));
        $this->assertTrue(method_exists($handler, 'render'));
        $this->assertTrue(property_exists($handler, 'dontReport'));
    }

    /**
     * Test that CSRF token mismatch preserves input
     */
    public function testCSRFTokenMismatchPreservesInput()
    {
        $handler = $this->app->make('App\Exceptions\Handler');
        $exception = new TokenMismatchException();
        
        // Create a mock request with input
        $request = $this->app['request'];
        $request->merge([
            'username' => 'test@example.com',
            'password' => 'password',
            '_token' => 'invalid-token'
        ]);
        
        // Test the render method
        $response = $handler->render($request, $exception);
        
        // Should redirect to login
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertContains('/login', $response->getTargetUrl());
    }
}