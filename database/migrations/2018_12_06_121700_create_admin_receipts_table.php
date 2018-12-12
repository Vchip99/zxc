<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminReceiptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_receipts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('receipt_by');
            $table->string('address');
            $table->string('gstin');
            $table->string('cin');
            $table->string('pan');
            $table->tinyInteger('is_gst_test_applied');
            $table->tinyInteger('is_gst_course_applied');
            $table->tinyInteger('is_gst_vkit_applied');
            $table->string('hsn_sac');
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
        Schema::drop('admin_receipts');
    }
}
