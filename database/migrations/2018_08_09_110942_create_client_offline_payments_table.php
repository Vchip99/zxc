<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientOfflinePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql2')->create('client_offline_payments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('client_batch_id');
            $table->integer('clientuser_id');
            $table->string('amount');
            $table->string('comment')->nullable();
            $table->string('due_date')->nullable();
            $table->integer('client_id');
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
        Schema::connection('mysql2')->drop('client_offline_payments');
    }
}
