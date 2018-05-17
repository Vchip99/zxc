<?php

use Illuminate\Database\Seeder;

class PlanTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('plans')->insert([
            ['id' => 1,'name' => 'free','amount' => '0','created_at' => NULL,'updated_at' => NULL],
            ['id' => 2,'name' => 'gold','amount' => '2999','created_at' => NULL,'updated_at' => NULL],
            ['id' => 3,'name' => 'platinum','amount' => '4999','created_at' => NULL,'updated_at' => NULL],
            ['id' => 4,'name' => 'diamond','amount' => '9999','created_at' => NULL,'updated_at' => NULL],
        ]);
    }
}
