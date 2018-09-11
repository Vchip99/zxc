<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddClientSmsCountColumnsToClientTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::connection('mysql2')->table('clients', function (Blueprint $table) {
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
      Schema::connection('mysql2')->table('clients', function (Blueprint $table) {
        $table->dropColumn('academic_sms_count');
        $table->dropColumn('message_sms_count');
        $table->dropColumn('lecture_sms_count');
        $table->dropColumn('otp_sms_count');
        $table->dropColumn('debit_sms_count');
        $table->dropColumn('credit_sms_count');
      });
    }
}
