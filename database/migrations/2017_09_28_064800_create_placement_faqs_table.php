<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlacementFaqsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('placement_faqs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('placement_area_id')->unsigned();
            $table->integer('placement_company_id')->unsigned();
            $table->text('question');
            $table->text('answer');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('placement_faqs');
    }
}
