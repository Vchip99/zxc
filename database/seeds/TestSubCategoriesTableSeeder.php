<?php

use Illuminate\Database\Seeder;

class TestSubCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('test_sub_categories')->insert([
        	['name' => 'Aptitude', 'test_category_id' => 1],
        	['name' => 'Quantity', 'test_category_id' => 1],
        	['name' => 'English', 'test_category_id' => 1],
        	['name' => 'ME', 'test_category_id' => 2],
        	['name' => 'EE', 'test_category_id' => 2],
        	['name' => 'CE', 'test_category_id' => 2],
        	['name' => 'ECE', 'test_category_id' => 2],
        	['name' => 'CS', 'test_category_id' => 2],
        	['name' => 'ME', 'test_category_id' => 3],
        	['name' => 'EE', 'test_category_id' => 3],
        	['name' => 'CE', 'test_category_id' => 3],
        	['name' => 'ECE', 'test_category_id' => 3],
        	['name' => 'PO', 'test_category_id' => 4],
        	['name' => 'Clerk', 'test_category_id' => 4],
        	['name' => 'Aptitude', 'test_category_id' => 5],
        	['name' => 'Quantity', 'test_category_id' => 5],
        	['name' => 'English', 'test_category_id' => 5]
    	]);
    }
}
