<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePayableClientSubCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql2')->create('payable_client_sub_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('client_id');
            $table->integer('category_id');
            $table->integer('sub_category_id');
            $table->string('admin_price');
            $table->string('client_user_price');
            $table->string('payament_id');
            $table->string('payament_request_id');
            $table->string('client_image');
            $table->date('start_date');
            $table->date('end_date');
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
        Schema::connection('mysql2')->drop('payable_client_sub_categories');
    }
}
