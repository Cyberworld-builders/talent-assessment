<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Closure;

class HttpsProtocol {

	public function handle(Request $request, Closure $next)
	{
		if (!$request->secure()) {
			return redirect()->secure($request->getRequestUri(), 301);
		}

		return $next($request);
	}
}