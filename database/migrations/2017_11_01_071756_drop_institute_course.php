<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropInstituteCourse extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql2')->table('client_assignment_subjects', function (Blueprint $table) {
            $table->dropColumn('client_institute_course_id');
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
        });
        Schema::connection('mysql2')->table('client_assignment_topics', function (Blueprint $table) {
            $table->dropColumn('client_institute_course_id');
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
        });
        Schema::connection('mysql2')->table('client_assignment_questions', function (Blueprint $table) {
            $table->dropColumn('client_institute_course_id');
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql2')->table('client_assignment_subjects', function (Blueprint $table) {
            $table->dropColumn('client_institute_course_id');
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
        });
        Schema::connection('mysql2')->table('client_assignment_topics', function (Blueprint $table) {
            $table->dropColumn('client_institute_course_id');
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
        });
        Schema::connection('mysql2')->table('client_assignment_questions', function (Blueprint $table) {
            $table->dropColumn('client_institute_course_id');
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
        });
    }
}
