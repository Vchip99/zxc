<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVerificationCodeColumsToTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('test_subject_papers', function (Blueprint $table) {
            $table->integer('verification_code_count')->nullable();
            $table->text('verification_code')->nullable();
        });
        Schema::table('scores', function (Blueprint $table) {
            $table->text('verification_code')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('test_subject_papers', function (Blueprint $table) {
            $table->dropColumn('verification_code_count');
            $table->dropColumn('verification_code');
        });
        Schema::table('scores', function (Blueprint $table) {
            $table->dropColumn('verification_code');
        });
    }
}
