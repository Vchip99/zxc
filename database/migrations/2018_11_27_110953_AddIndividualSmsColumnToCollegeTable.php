<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndividualSmsColumnToCollegeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('colleges', function (Blueprint $table) {
            $table->tinyInteger('individual_sms')->default(0)->after('lecture_sms');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('colleges', function (Blueprint $table) {
            $table->dropColumn('individual_sms');
        });
    }
}
