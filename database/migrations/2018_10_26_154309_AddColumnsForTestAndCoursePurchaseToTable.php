<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsForTestAndCoursePurchaseToTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('register_papers', function (Blueprint $table) {
            $table->string('payment_id');
            $table->string('payment_request_id');
            $table->string('price')->default(0);
        });
        Schema::table('register_online_courses', function (Blueprint $table) {
            $table->string('payment_id');
            $table->string('payment_request_id');
            $table->string('price')->default(0);
        });
        Schema::table('plans', function (Blueprint $table) {
            $table->string('monthly_amount');
        });
        Schema::table('course_videos', function (Blueprint $table) {
            $table->string('is_free')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('register_papers', function (Blueprint $table) {
            $table->dropColumn('payment_id');
            $table->dropColumn('payment_request_id');
            $table->dropColumn('price');
        });
        Schema::table('register_online_courses', function (Blueprint $table) {
            $table->dropColumn('payment_id');
            $table->dropColumn('payment_request_id');
            $table->dropColumn('price');
        });
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn('monthly_amount');
        });
        Schema::table('course_videos', function (Blueprint $table) {
            $table->dropColumn('is_free');
        });
    }
}
