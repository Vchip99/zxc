<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPriceAndSubCategoryIdColumnToTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('test_sub_categories', function (Blueprint $table) {
            $table->string('price')->default(0);
        });
        Schema::table('register_papers', function (Blueprint $table) {
            $table->integer('test_sub_category_id')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('test_sub_categories', function (Blueprint $table) {
            $table->dropColumn('price');
        });
        Schema::table('register_papers', function (Blueprint $table) {
            $table->dropColumn('test_sub_category_id');
        });
    }
}
