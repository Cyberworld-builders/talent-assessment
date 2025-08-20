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
		else
		{
			// Use default database configuration when no reseller session exists
			\Config::set('database.connections.mysql.host', env('DB_HOST', 'mysql-staging'));
			\Config::set('database.connections.mysql.database', env('DB_DATABASE', 'talent_assessment_staging'));
			\Config::set('database.connections.mysql.username', env('DB_USERNAME', 'talent_user_staging'));
			\Config::set('database.connections.mysql.password', env('DB_PASSWORD', 'strong_staging_db_pass_ntcneex7'));
			DB::reconnect('mysql');
		}

        return $next($request);
    }
}
