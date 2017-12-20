<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWebdevelopmentPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('webdevelopment_payments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('domains');
            $table->string('email');
            $table->string('phone');
            $table->string('payment_id');
            $table->string('payment_request_id');
            $table->string('status');
            $table->integer('price');
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
        Schema::drop('webdevelopment_payments');
    }
}
