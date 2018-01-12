<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientUserPurchasedTestSubCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql2')->create('client_user_purchased_test_sub_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('test_category_id');
            $table->integer('test_sub_category_id');
            $table->integer('client_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql2')->drop('client_user_purchased_test_sub_categories');
    }
}
