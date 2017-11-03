<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropInstituteCourseToClientOnlinePaperSections extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql2')->table('client_online_paper_sections', function (Blueprint $table) {
            $table->dropColumn('client_institute_course_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql2')->table('client_online_paper_sections', function (Blueprint $table) {
            $table->dropColumn('client_institute_course_id');
        });
    }
}
