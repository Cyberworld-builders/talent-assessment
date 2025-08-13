<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Call other seeders
        $this->call([
            DimensionsTableSeeder::class,
        ]);
    }
}
