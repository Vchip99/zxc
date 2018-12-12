<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAdminIdColumnToCourseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('course_courses', function (Blueprint $table) {
            $table->integer('admin_id')->default(NULL);
        });
        Schema::table('study_material_subjects', function (Blueprint $table) {
            $table->integer('admin_id');
        });
        DB::statement('UPDATE course_courses SET created_by = 0 WHERE admin_id = 1');
        DB::statement('UPDATE test_sub_categories SET created_by = 1 WHERE created_for = 1');
        DB::statement('UPDATE vkit_projects SET created_by = 1 WHERE created_for = 1');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('course_courses', function (Blueprint $table) {
            $table->dropColumn('admin_id');
        });
        Schema::table('study_material_subjects', function (Blueprint $table) {
            $table->dropColumn('admin_id');
        });
        DB::statement('UPDATE test_sub_categories SET created_by = 0 WHERE created_for = 1');
        DB::statement('UPDATE vkit_projects SET created_by = 0 WHERE created_for = 1');
    }
}
