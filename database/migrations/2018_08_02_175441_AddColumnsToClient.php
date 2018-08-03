<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToClient extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql2')->table('clients', function (Blueprint $table) {
           $table->string('allow_non_verified_email')->default(1);
        });
        Schema::connection('mysql2')->table('clientusers', function (Blueprint $table) {
           $table->string('number_verified')->default(0);
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
            $table->dropColumn('allow_non_verified_email')->nullable();
        });
        Schema::connection('mysql2')->table('clientusers', function (Blueprint $table) {
            $table->dropColumn('number_verified')->nullable();
        });
    }
}
