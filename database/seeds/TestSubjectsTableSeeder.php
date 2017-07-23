<?php

use Illuminate\Database\Seeder;

class TestSubjectsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('test_subjects')->insert([
        	['name' => 'Subject 1', 'test_sub_category_id' => 1],
        	['name' => 'Subject 2', 'test_sub_category_id' => 2],
        	['name' => 'Subject 3', 'test_sub_category_id' => 3],
        	['name' => 'Subject 4', 'test_sub_category_id' => 3],
        	['name' => 'Subject 5', 'test_sub_category_id' => 5],
        	['name' => 'Subject 6', 'test_sub_category_id' => 6],
        	['name' => 'Subject 7', 'test_sub_category_id' => 7],
        	['name' => 'Subject 8', 'test_sub_category_id' => 8],
        	['name' => 'Subject 9', 'test_sub_category_id' => 9],
        	['name' => 'Subject 10', 'test_sub_category_id' => 10],
        	['name' => 'Subject 11', 'test_sub_category_id' => 11],
        	['name' => 'Subject 12', 'test_sub_category_id' => 12],
        	['name' => 'Subject 13', 'test_sub_category_id' => 13],
        	['name' => 'Subject 14', 'test_sub_category_id' => 14],
        	['name' => 'Subject 15', 'test_sub_category_id' => 15],
    	]);
    }
}
