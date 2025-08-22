<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(App\User::class, function (Faker\Generator $faker) {
    return [
        'username' => $faker->unique()->userName,
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => bcrypt('password'),
        'remember_token' => str_random(10),
    ];
});

$factory->define(App\Industry::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->unique()->company,
    ];
});

$factory->define(App\Assessment::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->sentence(3),
        'description' => $faker->paragraph,
        'logo' => '',
        'background' => '',
        'paginate' => 10,
        'items_per_page' => 10,
        'timed' => 0,
        'use_custom_fields' => 0,
        'target' => 1,
        'last_modified' => \Carbon\Carbon::now(),
    ];
});

$factory->define(App\Dimension::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->word,
        'parent' => 0,
        'code' => strtoupper($faker->lexify('???')),
    ];
});

$factory->define(App\Benchmark::class, function (Faker\Generator $faker) {
    return [
        'value' => $faker->numberBetween(50, 100),
        'dimension_id' => function () {
            return factory(App\Dimension::class)->create()->id;
        },
        'industry_id' => function () {
            return factory(App\Industry::class)->create()->id;
        },
    ];
});
