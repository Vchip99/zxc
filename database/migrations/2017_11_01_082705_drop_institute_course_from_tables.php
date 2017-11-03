<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropInstituteCourseFromTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql2')->table('client_notifications', function (Blueprint $table) {
            $table->dropColumn('client_institute_course_id');
        });
        Schema::connection('mysql2')->table('client_online_videos', function (Blueprint $table) {
            $table->dropColumn('client_institute_course_id');
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
        });
        Schema::connection('mysql2')->table('client_scores', function (Blueprint $table) {
            $table->dropColumn('client_institute_course_id');
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
        });
        Schema::connection('mysql2')->dropIfExists('client_user_institute_courses');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql2')->table('client_notifications', function (Blueprint $table) {
            $table->dropColumn('client_institute_course_id');
        });
        Schema::connection('mysql2')->table('client_online_videos', function (Blueprint $table) {
            $table->dropColumn('client_institute_course_id');
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
        });
        Schema::connection('mysql2')->table('client_scores', function (Blueprint $table) {
            $table->dropColumn('client_institute_course_id');
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
        });
        Schema::connection('mysql2')->dropIfExists('client_user_institute_courses');
    }
}
