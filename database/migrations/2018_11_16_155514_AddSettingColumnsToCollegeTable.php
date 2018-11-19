<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSettingColumnsToCollegeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('colleges', function (Blueprint $table) {
            $table->tinyInteger('absent_sms')->default(0);
            $table->tinyInteger('exam_sms')->default(0);
            $table->tinyInteger('offline_exam_sms')->default(0);
            $table->string('notice_sms')->nullable();
            $table->string('emergency_notice_sms')->nullable();
            $table->string('holiday_sms')->nullable();
            $table->tinyInteger('assignment_sms')->default(0);
            $table->tinyInteger('lecture_sms')->default(0);
            $table->integer('academic_sms_count')->default(0);
            $table->integer('message_sms_count')->default(0);
            $table->integer('lecture_sms_count')->default(0);
            $table->integer('otp_sms_count')->default(0);
            $table->integer('debit_sms_count')->default(0);
            $table->integer('credit_sms_count')->default(0);
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
            $table->dropColumn('absent_sms');
            $table->dropColumn('exam_sms');
            $table->dropColumn('offline_exam_sms');
            $table->dropColumn('notice_sms');
            $table->dropColumn('emergency_notice_sms');
            $table->dropColumn('holiday_sms');
            $table->dropColumn('assignment_sms');
            $table->dropColumn('lecture_sms');
            $table->dropColumn('academic_sms_count');
            $table->dropColumn('message_sms_count');
            $table->dropColumn('lecture_sms_count');
            $table->dropColumn('otp_sms_count');
            $table->dropColumn('debit_sms_count');
            $table->dropColumn('credit_sms_count');
        });
    }
}
