<?php

use Illuminate\Database\Seeder;
use App\Industry;

class IndustriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $industries = [
            'Technology',
            'Healthcare',
            'Finance',
            'Education',
            'Manufacturing',
            'Retail',
            'Consulting',
            'Government',
            'Non-Profit',
            'Real Estate',
            'Transportation',
            'Energy',
            'Media',
            'Legal',
            'Hospitality'
        ];

        foreach ($industries as $industry) {
            Industry::create(['name' => $industry]);
        }
    }
}

