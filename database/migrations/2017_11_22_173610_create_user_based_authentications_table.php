<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserBasedAuthenticationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql2')->create('user_based_authentications', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('vchip_client_id');
            $table->string('instamojo_client_id');
            $table->string('access_token')->default(NULL);
            $table->string('refresh_token')->default(NULL);
            $table->string('token_type')->default(NULL);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql2')->drop('user_based_authentications');
    }
}
