<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorkshopDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('workshop_details', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('workshop_category_id')->unsigned();
            $table->string('workshop_image');
            $table->string('author');
            $table->text('author_introduction');
            $table->string('author_image');
            $table->text('description');
            $table->boolean('certified');
            $table->date('start_date');
            $table->date('end_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('workshop_details');
    }
}
