<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMonthlyPriceColumsToClientOnlineTestSubCategory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql2')->table('client_online_test_sub_categories', function (Blueprint $table) {
            $table->string('monthly_price')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql2')->table('client_online_test_sub_categories', function (Blueprint $table) {
           $table->dropColumn('monthly_price');
           $table->dropColumn('created_at');
           $table->dropColumn('updated_at');
        });
    }
}
