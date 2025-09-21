<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Auth\Access\UnauthorizedException;
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
        UnauthorizedException::class,
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

        // Handle CSRF token mismatch gracefully - always redirect to login for security
        if ($e instanceof TokenMismatchException) {
            // Log the CSRF issue for debugging (but don't report as error)
            \Log::info('CSRF token mismatch', [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'ip' => $request->ip(),
                'user_agent' => $request->header('User-Agent', 'Unknown')
            ]);
            
            // Invalidate the session to force re-authentication
            \Session::flush();
            \Auth::logout();
            
            // Always redirect to login for CSRF token mismatches to force re-authentication
            return redirect()->to('/login')
                ->withInput($request->except('_token', 'password', 'password_confirmation'))
                ->withErrors(['_token' => 'Your session has expired. Please log in again.']);
        }

        // Handle authentication exceptions gracefully
        if ($e instanceof UnauthorizedException) {
            return redirect()->to('/login')
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
