<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_plans', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('client_id');
            $table->integer('plan_id');
            $table->string('plan_amount');
            $table->string('final_amount');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('payment_status');
            $table->tinyInteger('degrade_plan')->default(0);
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
        Schema::drop('client_plans');
    }
}
