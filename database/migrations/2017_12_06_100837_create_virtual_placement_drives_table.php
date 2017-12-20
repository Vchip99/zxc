<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVirtualPlacementDrivesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('virtual_placement_drives', function (Blueprint $table) {
            $table->increments('id');
            $table->text('about');
            $table->string('about_image');
            $table->text('online_test');
            $table->text('ti_basic');
            $table->text('ti_machine_test');
            $table->text('ti_puzzle');
            $table->text('gd');
            $table->text('pi');
            $table->text('program_arrangement');
            $table->string('program_arrangement_image');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('virtual_placement_drives');
    }
}
