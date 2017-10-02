<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompanyDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('placement_area_id')->unsigned();
            $table->integer('placement_company_id')->unsigned();
            $table->text('about_company');
            $table->string('industry_type');
            $table->string('founded_year');
            $table->string('founder_name');
            $table->string('headquarters');
            $table->string('ceo');
            $table->text('products');
            $table->string('website');
            $table->string('mock_test_link');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('company_details');
    }
}
