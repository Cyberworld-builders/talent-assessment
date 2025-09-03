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
		$this->call(Involved360AssessmentSeeder::class);

        Model::reguard();
    }
}
