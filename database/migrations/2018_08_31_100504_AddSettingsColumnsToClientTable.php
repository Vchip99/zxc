<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSettingsColumnsToClientTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql2')->table('clients', function (Blueprint $table) {
            $table->string('absent_sms')->default(4);
            $table->string('exam_sms')->default(4);
            $table->string('offline_exam_sms')->default(4);
            $table->string('notice_sms')->default(4);
            $table->string('emergency_notice_sms')->default(4);
            $table->string('holiday_sms')->default(4);
            $table->string('assignment_sms')->default(4);
            $table->tinyInteger('lecture_sms')->default(0);
            $table->string('individual_sms')->default(4);
            $table->string('login_using')->default(3);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql2')->table('clients', function (Blueprint $table) {
            $table->dropColumn('absent_sms');
            $table->dropColumn('exam_sms');
            $table->dropColumn('offline_exam_sms');
            $table->dropColumn('notice_sms');
            $table->dropColumn('emergency_notice_sms');
            $table->dropColumn('holiday_sms');
            $table->dropColumn('assignment_sms');
            $table->dropColumn('lecture_sms');
            $table->dropColumn('individual_sms');
            $table->dropColumn('login_using');
        });
    }
}