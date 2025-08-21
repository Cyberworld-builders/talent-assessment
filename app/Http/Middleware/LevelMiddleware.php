<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class LevelMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  int  $level
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $level)
    {
        if (!$request->user() || $request->user()->level() < $level) {
            \Auth::logout();
            return redirect('/login');
        }

        return $next($request);
    }
}
