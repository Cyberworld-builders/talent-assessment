<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

		$this->call(LanguageTableSeeder::class);
		$this->call(RoleTableSeeder::class);
		$this->call(UserTableSeeder::class);
		$this->call(IndustriesTableSeeder::class);
		
		// Only run Involved360AssessmentSeeder in non-testing environments
		// Check multiple ways to detect testing environment
		$isTesting = app()->environment() === 'testing' || 
					 env('APP_ENV') === 'testing' || 
					 config('database.default') === 'sqlite' ||
					 config('database.connections.mysql.database') === 'testing';
		
		if (!$isTesting) {
			$this->call(Involved360AssessmentSeeder::class);
		}

        Model::reguard();
    }
}
