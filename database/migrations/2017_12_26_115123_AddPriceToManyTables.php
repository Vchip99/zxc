<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPriceToManyTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql2')->table('client_user_purchased_courses', function (Blueprint $table) {
            $table->string('price');
        });
        Schema::connection('mysql2')->table('client_user_purchased_test_sub_categories', function (Blueprint $table) {
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
        Schema::connection('mysql2')->table('client_user_purchased_courses', function (Blueprint $table) {
            $table->dropColumn('price');
        });
        Schema::connection('mysql2')->table('client_user_purchased_test_sub_categories', function (Blueprint $table) {
            $table->dropColumn('price');
        });

    }
}
