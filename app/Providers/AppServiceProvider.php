<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Only force HTTPS in non-local environments
        if (env('APP_ENV') !== 'local') {
            URL::forceScheme('https');
        }
        view()->composer('*', 'App\Http\ViewComposers\DashboardComposer');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
