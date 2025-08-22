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
            // Ensure we have a valid user_id
            if (!isset($attributes['user_id'])) {
                // Try to find an existing user first
                $existingUser = App\User::first();
                if (!$existingUser) {
                    // Create a default user if none exists
                    $existingUser = App\User::create([
                        'username' => 'testuser_' . uniqid(),
                        'name' => 'Test User',
                        'email' => 'test_' . uniqid() . '@example.com',
                        'password' => bcrypt('password'),
                    ]);
                }
                $attributes['user_id'] = $existingUser->id;
            }
            
            $data = array_merge([
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
            ], $attributes);
            

            
            return App\Assessment::create($data);
        }
        
        if ($class === App\Dimension::class || $class === 'App\Dimension') {
            // Ensure we have a valid assessment_id
            if (!isset($attributes['assessment_id'])) {
                // Find an existing assessment or create one directly
                $existingAssessment = App\Assessment::first();
                if (!$existingAssessment) {
                    // Create a default assessment directly without using factory
                    $existingUser = App\User::first();
                    if (!$existingUser) {
                        $existingUser = App\User::create([
                            'username' => 'testuser_' . uniqid(),
                            'name' => 'Test User',
                            'email' => 'test_' . uniqid() . '@example.com',
                            'password' => bcrypt('password'),
                        ]);
                    }
                    $existingAssessment = App\Assessment::create([
                        'name' => 'Test Assessment',
                        'description' => 'Test Description',
                        'user_id' => $existingUser->id,
                        'logo' => '',
                        'background' => '',
                        'paginate' => 10,
                        'items_per_page' => 10,
                        'timed' => 0,
                        'use_custom_fields' => 0,
                        'target' => 1,
                        'last_modified' => \Carbon\Carbon::now(),
                    ]);
                }
                $attributes['assessment_id'] = $existingAssessment->id;
            }
            
            return App\Dimension::create(array_merge([
                'name' => 'Test Dimension',
                'parent' => 0,
                'code' => 'TST',
            ], $attributes));
        }
        
        if ($class === App\Industry::class || $class === 'App\Industry') {
            // Use firstOrCreate to avoid duplicate entries
            $name = $attributes['name'] ?? 'Test Industry';
            return App\Industry::firstOrCreate(
                ['name' => $name],
                array_merge([
                    'name' => $name,
                ], $attributes)
            );
        }
        
        if ($class === App\Benchmark::class || $class === 'App\Benchmark') {
            // Ensure we have a valid industry_id
            if (!isset($attributes['industry_id'])) {
                // Find an existing industry or create one
                $existingIndustry = App\Industry::first();
                if (!$existingIndustry) {
                    $existingIndustry = App\Industry::firstOrCreate(
                        ['name' => 'Test Industry'],
                        ['name' => 'Test Industry']
                    );
                }
                $attributes['industry_id'] = $existingIndustry->id;
            }
            
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
