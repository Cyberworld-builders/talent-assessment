<?php

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| The first thing we will do is create a new Laravel application instance
| which serves as the "glue" for all the components of Laravel, and is
| the IoC container for the system binding all of the various parts.
|
*/

$app = new Illuminate\Foundation\Application(
    realpath(__DIR__.'/../')
);

/*
|--------------------------------------------------------------------------
| Bind Important Interfaces
|--------------------------------------------------------------------------
|
| Next, we need to bind some important interfaces into the container so
| we will be able to resolve them when needed. The kernels serve the
| incoming requests to this application from both the web and CLI.
|
*/

$app->singleton(
    Illuminate\Contracts\Http\Kernel::class,
    App\Http\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

/*
|--------------------------------------------------------------------------
| Global factory() function for backward compatibility with Laravel 5.1 tests
|--------------------------------------------------------------------------
*/
if (!function_exists('factory')) {
    function factory($class, $attributes = [])
    {
        // Simple factory function for backward compatibility
        if ($class === App\User::class || $class === 'App\User') {
            return App\User::create(array_merge([
                'username' => 'testuser_' . uniqid(),
                'name' => 'Test User',
                'email' => 'test_' . uniqid() . '@example.com',
                'password' => bcrypt('password'),
            ], $attributes));
        }
        
        if ($class === App\Assessment::class || $class === 'App\Assessment') {
            return App\Assessment::create(array_merge([
                'name' => 'Test Assessment',
                'description' => 'Test Description',
                'logo' => '',
                'background' => '',
                'paginate' => 10,
                'items_per_page' => 10,
                'timed' => 0,
                'use_custom_fields' => 0,
                'target' => 1,
                'last_modified' => \Carbon\Carbon::now(),
                'user_id' => 1, // Default user_id (will be overridden if provided in attributes)
            ], $attributes));
        }
        
        if ($class === App\Dimension::class || $class === 'App\Dimension') {
            return App\Dimension::create(array_merge([
                'name' => 'Test Dimension',
                'parent' => 0,
                'code' => 'TST',
            ], $attributes));
        }
        
        if ($class === App\Industry::class || $class === 'App\Industry') {
            return App\Industry::create(array_merge([
                'name' => 'Test Industry',
            ], $attributes));
        }
        
        if ($class === App\Benchmark::class || $class === 'App\Benchmark') {
            return App\Benchmark::create(array_merge([
                'value' => 75,
            ], $attributes));
        }
        
        // Default fallback
        return new $class($attributes);
    }
}

/*
|--------------------------------------------------------------------------
| Return The Application
|--------------------------------------------------------------------------
|
| This script returns the application instance. The instance is given to
| the calling script so we can separate the building of the instances
| from the actual running of the application and sending responses.
|
*/

return $app;
