<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCollegePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('college_payments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('college_id');
            $table->integer('user_id');
            $table->string('user_name');
            $table->string('note');
            $table->string('payment_id');
            $table->string('payment_request_id');
            $table->string('price');
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
        Schema::drop('college_payments');
    }
}
