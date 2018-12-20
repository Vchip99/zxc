<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPaperPatternColumnToClientTestPaper extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('test_subject_papers', function (Blueprint $table) {
            $table->tinyInteger('paper_pattern')->default(0);
        });
        Schema::connection('mysql2')->table('client_online_test_subject_papers', function (Blueprint $table) {
            $table->tinyInteger('paper_pattern')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('test_subject_papers', function (Blueprint $table) {
           $table->dropColumn('paper_pattern');
        });
        Schema::connection('mysql2')->table('client_online_test_subject_papers', function (Blueprint $table) {
           $table->dropColumn('paper_pattern');
        });
    }
}
