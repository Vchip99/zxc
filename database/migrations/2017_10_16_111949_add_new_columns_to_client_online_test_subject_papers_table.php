<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewColumnsToClientOnlineTestSubjectPapersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql2')->table('client_online_test_subject_papers', function (Blueprint $table) {
            $table->integer('option_count')->default(4);
            $table->date('date_to_inactive')->default('2050-01-01');
            $table->tinyInteger('time_out_by')->default(1);
            $table->tinyInteger('show_calculator')->default(1);
            $table->tinyInteger('show_solution')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql2')->table('client_online_test_subject_papers', function (Blueprint $table) {
            $table->dropColumn('option_count');
            $table->dropColumn('date_to_inactive');
            $table->dropColumn('time_out_by');
            $table->dropColumn('show_calculator');
            $table->dropColumn('show_solution');
        });
    }
}
