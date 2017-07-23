<?php

use Illuminate\Database\Seeder;

class TestSubjectPapersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('test_subject_papers')->insert([
        	['name' => 'paper1', 'test_subject_id' => 1],
        	['name' => 'paper2', 'test_subject_id' => 2],
        	['name' => 'paper1', 'test_subject_id' => 3],
        	['name' => 'paper2', 'test_subject_id' => 4],
        	['name' => 'paper1', 'test_subject_id' => 5],
        	['name' => 'paper2', 'test_subject_id' => 6],
        	['name' => 'paper1', 'test_subject_id' => 7],
        	['name' => 'paper2', 'test_subject_id' => 7],
        	['name' => 'paper1', 'test_subject_id' => 9],
        	['name' => 'paper2', 'test_subject_id' => 10],
            ['name' => 'paper1', 'test_subject_id' => 11],
            ['name' => 'paper2', 'test_subject_id' => 12],
            ['name' => 'paper1', 'test_subject_id' => 13],
            ['name' => 'paper2', 'test_subject_id' => 14],
            ['name' => 'paper1', 'test_subject_id' => 15],

    	]);
    }
}
