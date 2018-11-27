<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToClientExamAndClientOfflinePaperMarkTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql2')->table('client_exams', function (Blueprint $table) {
            $table->string('marks')->default(0);
            $table->tinyInteger('exam_type')->default(0);
        });
        DB::connection('mysql2')->statement('ALTER TABLE client_offline_paper_marks change client_offline_paper_id client_exam_id Integer(11)');
        Schema::connection('mysql2')->drop('client_offline_papers');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql2')->table('client_exams', function (Blueprint $table) {
            $table->dropColumn('marks');
            $table->dropColumn('exam_type');
        });
        DB::connection('mysql2')->statement('ALTER TABLE client_offline_paper_marks change client_exam_id client_offline_paper_id Integer(11)');
    }
}
