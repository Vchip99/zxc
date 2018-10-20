<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCreatedForColumnToTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('course_sub_categories', function (Blueprint $table) {
            $table->tinyInteger('created_for')->default(1);
        });
        Schema::table('test_sub_categories', function (Blueprint $table) {
            $table->tinyInteger('created_for')->default(1);
        });
        Schema::table('vkit_projects', function (Blueprint $table) {
            $table->tinyInteger('created_for')->default(1);
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
            $table->dropColumn('created_for');
        });
        Schema::table('test_sub_categories', function (Blueprint $table) {
            $table->dropColumn('created_for');
        });
        Schema::table('vkit_projects', function (Blueprint $table) {
            $table->dropColumn('created_for');
        });
    }
}
