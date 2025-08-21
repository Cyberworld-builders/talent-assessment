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
        // Disable foreign key checks during seeding
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Call other seeders
        $this->call([
            DimensionsTableSeeder::class,
        ]);

        // Re-enable foreign key checks
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
