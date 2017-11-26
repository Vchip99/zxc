<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInstamojoDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('instamojo_details', function (Blueprint $table) {
            $table->increments('id');
            $table->text('client_id');
            $table->text('client_secret');
            $table->text('referrer');
            $table->text('application_base_access_token');
            $table->text('application_base_token_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('instamojo_details');
    }
}
