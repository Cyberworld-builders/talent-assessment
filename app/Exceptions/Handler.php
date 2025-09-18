<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        HttpException::class,
        ModelNotFoundException::class,
        TokenMismatchException::class,
        AuthenticationException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        return parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        if ($e instanceof ModelNotFoundException) {
            $e = new NotFoundHttpException($e->getMessage(), $e);
        }

//        if ($request->isJson() || $request->isXmlHttpRequest()) {
//            return Response::json([
//                'error' => [
//                    'exception' => class_basename( $e ) . ' in ' . basename( $e->getFile() ) . ' line ' . $e->getLine() . ': ' . $e->getMessage(),
//                ]
//            ], 500);
//        }

        // Handle CSRF token mismatch gracefully - redirect to login instead of 500 error
        if ($e instanceof TokenMismatchException) {
            // Log the CSRF issue for debugging (but don't report as error)
            \Log::info('CSRF token mismatch', [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
            
            // If we can't go back (e.g., direct POST to login), redirect to login page
            if ($request->is('login') || !$request->hasSession() || !$request->session()->has('_previous')) {
                return redirect()->route('login')
                    ->withInput($request->except('_token'))
                    ->withErrors(['_token' => 'Your session has expired. Please try again.']);
            }
            
            return redirect()->back()
                ->withInput($request->except('_token'))
                ->withErrors(['_token' => 'Your session has expired. Please try again.']);
        }

        // Handle authentication exceptions gracefully
        if ($e instanceof AuthenticationException) {
            return redirect()->route('login')
                ->withErrors(['auth' => 'Please log in to continue.']);
        }

        // If you don't have sufficient permissions to view route
        if ($e instanceof \Bican\Roles\Exceptions\LevelDeniedException)
        {
            if (\Auth::user()->level() < 2)
                return redirect('/assignments');

            return redirect()->back();
        }

        return parent::render($request, $e);
    }
}
