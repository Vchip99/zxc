<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOfflineWorkshopDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offline_workshop_details', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('offline_workshop_category_id');
            $table->text('about');
            $table->string('about_image');
            $table->text('benefits');
            $table->string('benefits_image');
            $table->string('duration');
            $table->text('topics');
            $table->text('projects');
            $table->text('prerequisite');
            $table->text('attendees');
            $table->text('learn_reason');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('offline_workshop_details');
    }
}
