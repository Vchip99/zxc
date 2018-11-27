<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientReceiptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql2')->create('client_receipts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('offline_receipt_by');
            $table->string('offline_address');
            $table->string('offline_gstin');
            $table->string('offline_cin');
            $table->string('offline_pan');
            $table->tinyInteger('is_offline_gst_applied')->default(0);
            $table->tinyInteger('is_same_details')->default(0);
            $table->string('online_receipt_by');
            $table->string('online_address');
            $table->string('online_gstin');
            $table->string('online_cin');
            $table->string('online_pan');
            $table->tinyInteger('is_online_gst_applied')->default(0);
            $table->string('hsn_sac');
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
        Schema::connection('mysql2')->drop('client_receipts');
    }
}
