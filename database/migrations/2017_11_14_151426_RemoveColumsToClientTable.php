<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveColumsToClientTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql2')->table('clients', function (Blueprint $table) {
            $table->dropColumn('user_id');
            $table->dropColumn('verified');
            $table->dropColumn('email_token');
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
            $table->dropColumn('user_id');
            $table->dropColumn('verified');
            $table->dropColumn('email_token');
        });
    }
}
