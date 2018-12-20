<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAdminApproveColumnToTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('course_courses', function (Blueprint $table) {
            $table->integer('admin_approve')->default(1);
        });
        Schema::table('test_sub_categories', function (Blueprint $table) {
            $table->integer('admin_approve')->default(1);
        });
        Schema::table('vkit_projects', function (Blueprint $table) {
            $table->integer('admin_approve')->default(1);
        });
        Schema::table('study_material_subjects', function (Blueprint $table) {
            $table->integer('admin_approve')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('course_courses', function (Blueprint $table) {
            $table->dropColumn('admin_approve');
        });
        Schema::table('test_sub_categories', function (Blueprint $table) {
            $table->dropColumn('admin_approve');
        });
        Schema::table('vkit_projects', function (Blueprint $table) {
            $table->dropColumn('admin_approve');
        });
        Schema::table('study_material_subjects', function (Blueprint $table) {
            $table->dropColumn('admin_approve');
        });
    }
}
