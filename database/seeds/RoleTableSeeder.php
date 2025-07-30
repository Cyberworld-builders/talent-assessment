<?php

use Illuminate\Database\Seeder;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		DB::table('roles')->insert([
			'name' => 'AOE Admin',
			'slug' => 'admin',
			'level' => 4
		]);
		DB::table('roles')->insert([
			'name' => 'Reseller',
			'slug' => 'reseller',
			'level' => 3
		]);
		DB::table('roles')->insert([
			'name' => 'Client Admin',
			'slug' => 'client',
			'level' => 2
		]);
		DB::table('roles')->insert([
			'name' => 'User',
			'slug' => 'user',
			'level' => 1
		]);
    }
}
