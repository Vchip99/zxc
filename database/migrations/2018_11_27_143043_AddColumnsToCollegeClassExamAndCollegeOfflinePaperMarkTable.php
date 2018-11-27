<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToCollegeClassExamAndCollegeOfflinePaperMarkTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('college_class_exams', function (Blueprint $table) {
            $table->string('marks')->default(0);
            $table->tinyInteger('exam_type')->default(0);
        });
        DB::statement('ALTER TABLE college_offline_paper_marks change college_offline_paper_id college_class_exam_id Integer(11)');
        Schema::drop('college_offline_papers');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('college_class_exams', function (Blueprint $table) {
            $table->dropColumn('marks');
            $table->dropColumn('exam_type');
        });
        DB::statement('ALTER TABLE college_offline_paper_marks change college_class_exam_id college_offline_paper_id Integer(11)');
    }
}
