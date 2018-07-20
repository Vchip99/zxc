<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientOfflinePaperMarksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql2')->create('client_offline_paper_marks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('client_batch_id');
            $table->integer('client_offline_paper_id');
            $table->integer('clientuser_id');
            $table->string('marks');
            $table->string('total_marks');
            $table->integer('client_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql2')->drop('client_offline_paper_marks');
    }
}
