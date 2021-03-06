<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUnreadMessageToClientUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql2')->table('clientusers', function (Blueprint $table) {
            $table->string('unread_messages')->nullable();
            $table->string('user_type')->default(1);
            $table->string('assigned_modules')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql2')->table('clientusers', function (Blueprint $table) {
            $table->dropColumn('unread_messages');
            $table->dropColumn('user_type');
            $table->dropColumn('assigned_modules');
        });
    }
}
