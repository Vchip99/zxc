<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeColumnTypeToAssignmentTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE `assignment_topics` CHANGE `assignment_subject_id` `college_subject_id` INT(11) NOT NULL');
        DB::statement('ALTER TABLE `assignment_topics` CHANGE `college_dept_id` `college_dept_ids` VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE `assignment_topics` ADD `years` VARCHAR(255) NOT NULL AFTER `college_dept_ids`');
        DB::statement('ALTER TABLE `assignment_questions` DROP INDEX idx_college_id_college_dept_id');
        DB::statement('ALTER TABLE `assignment_questions` CHANGE `college_dept_id` `college_dept_ids` VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE `assignment_questions` CHANGE `year` `years` VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE `assignment_questions` CHANGE `assignment_subject_id` `college_subject_id` INT(11) NOT NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE `assignment_topics` CHANGE `college_subject_id` `assignment_subject_id` INT(11) NOT NULL');
        DB::statement('ALTER TABLE `assignment_topics` CHANGE `college_dept_ids` `college_dept_id` INT(11) NOT NULL');
        DB::statement('ALTER TABLE `assignment_topics` DROP `years`');
        DB::statement('ALTER TABLE `assignment_questions` CHANGE `college_dept_ids` `college_dept_id` INT(11) NOT NULL');
        DB::statement('ALTER TABLE `assignment_questions` CHANGE `years` `year` INT(11) NOT NULL');
        DB::statement('ALTER TABLE `assignment_questions` CHANGE `college_subject_id` `assignment_subject_id` INT(11) NOT NULL');
    }
}
