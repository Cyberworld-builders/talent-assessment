<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

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
			'level' => 4,
			'created_at' => Carbon::now(),
			'updated_at' => Carbon::now()
		]);
		DB::table('roles')->insert([
			'name' => 'Reseller',
			'slug' => 'reseller',
			'level' => 3,
			'created_at' => Carbon::now(),
			'updated_at' => Carbon::now()
		]);
		DB::table('roles')->insert([
			'name' => 'Client Admin',
			'slug' => 'client',
			'level' => 2,
			'created_at' => Carbon::now(),
			'updated_at' => Carbon::now()
		]);
		DB::table('roles')->insert([
			'name' => 'User',
			'slug' => 'user',
			'level' => 1,
			'created_at' => Carbon::now(),
			'updated_at' => Carbon::now()
		]);
    }
}
