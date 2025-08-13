<?php

use Illuminate\Database\Seeder;

class LanguageTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		DB::table('languages')->insert([
			'name' => 'English',
			'native_name' => 'English',
			'code' => 'en',
			'terms' => 'Terms and Conditions'
		]);
		DB::table('languages')->insert([
			'name' => 'Spanish',
			'native_name' => 'Español',
			'code' => 'es',
			'terms' => 'Términos y Condiciones'
		]);
    }
}
