<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('course_sub_categories', function (Blueprint $table) {
            $table->integer('created_by')->default(0);
            $table->string('created_by_name');
        });
        Schema::table('test_sub_categories', function (Blueprint $table) {
            $table->integer('created_by')->default(1);
            $table->string('created_by_name');
        });
        Schema::table('course_courses', function (Blueprint $table) {
            $table->integer('created_by')->default(0);
        });
        Schema::table('test_subjects', function (Blueprint $table) {
            $table->integer('created_by')->default(0);
        });
        Schema::table('vkit_projects', function (Blueprint $table) {
            $table->integer('created_by')->default(0);
        });
        Schema::table('users', function (Blueprint $table) {
            $table->string('assigned_college_depts');
        });
        Schema::table('assignment_answers', function (Blueprint $table) {
            $table->integer('student_dept_id');
        });
        Schema::table('college_categories', function (Blueprint $table) {
            $table->string('created_by_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('course_sub_categories', function (Blueprint $table) {
            $table->dropColumn('created_by');
            $table->dropColumn('created_by_name');
        });
        Schema::table('test_sub_categories', function (Blueprint $table) {
            $table->dropColumn('created_by');
            $table->dropColumn('created_by_name');
        });
        Schema::table('course_courses', function (Blueprint $table) {
            $table->dropColumn('created_by');
        });
        Schema::table('test_subjects', function (Blueprint $table) {
            $table->dropColumn('created_by');
        });
        Schema::table('vkit_projects', function (Blueprint $table) {
            $table->dropColumn('created_by');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('assigned_college_depts');
        });
        Schema::table('assignment_answers', function (Blueprint $table) {
            $table->dropColumn('student_dept_id');
        });
        Schema::table('college_categories', function (Blueprint $table) {
            $table->dropColumn('created_by_name');
        });
    }
}
