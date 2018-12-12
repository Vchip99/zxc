<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPaymentColumnsToVkitProjectTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vkit_projects', function (Blueprint $table) {
            $table->string('price');
            $table->text('items');
        });
        Schema::table('register_projects', function (Blueprint $table) {
            $table->string('payment_id');
            $table->string('payment_request_id');
            $table->string('price');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vkit_projects', function (Blueprint $table) {
            $table->removeColumn('price');
            $table->removeColumn('items');
        });
        Schema::table('register_projects', function (Blueprint $table) {
            $table->removeColumn('payment_id');
            $table->removeColumn('payment_request_id');
            $table->removeColumn('price');
        });
    }
}
