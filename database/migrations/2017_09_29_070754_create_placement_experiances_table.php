<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlacementExperiancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('placement_experiances', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('placement_area_id')->unsigned();
            $table->integer('placement_company_id')->unsigned();
            $table->string('title');
            $table->text('question');
            $table->integer('user_id')->unsigned();
            $table->dateTime('created_at');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('placement_experiances');
    }
}
