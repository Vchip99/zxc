<?php

use Illuminate\Database\Seeder;

class TestCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('test_categories')->insert([
        	['name' => 'CAT'],
        	['name' => 'GATE'],
        	['name' => 'IES'],
        	['name' => 'Banking'],
        	['name' => 'Aptitude'],
        	['name' => 'SSC'],
        	['name' => 'UPSC'],
        	['name' => 'Government'],
        	['name' => 'Others']
    	]);
    }
}
