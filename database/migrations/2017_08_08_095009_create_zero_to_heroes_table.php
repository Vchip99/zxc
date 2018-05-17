<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateZeroToHeroesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zero_to_heroes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('designation_id')->unsigned();
            $table->integer('area_id')->unsigned();
            $table->string('url');
            $table->date('release_date');
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
        Schema::drop('zero_to_heroes');
    }
}
