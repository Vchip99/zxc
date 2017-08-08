<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPhotoToClientusers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql2')->table('clientusers', function (Blueprint $table) {
            $table->string('photo')->nullable();
            $table->string('resume')->nullable();
            $table->string('recorded_video')->nullable();
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
            $table->dropColumn('photo');
            $table->dropColumn('resume');
            $table->dropColumn('recorded_video');
        });
    }
}
