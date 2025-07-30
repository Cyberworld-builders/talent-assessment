<?php

namespace App\Http\Middleware;

use Closure;
use DB;

class SetDatabase
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
		if (session('reseller'))
		{
			\Config::set('database.connections.mysql.host', session('reseller')->getDbHost());
			\Config::set('database.connections.mysql.database', session('reseller')->getDbName());
			\Config::set('database.connections.mysql.username', session('reseller')->getDbUser());
			\Config::set('database.connections.mysql.password', session('reseller')->getDbPass());
			DB::reconnect('mysql');
		}

        return $next($request);
    }
}
