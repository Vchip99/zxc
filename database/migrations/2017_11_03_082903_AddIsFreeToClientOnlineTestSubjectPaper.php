<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsFreeToClientOnlineTestSubjectPaper extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql2')->table('client_online_test_subject_papers', function (Blueprint $table) {
            $table->dropColumn('price');
            $table->tinyInteger('is_free')->default(0);
            $table->tinyInteger('allowed_unauthorised_user')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql2')->table('client_online_test_subject_papers', function (Blueprint $table) {
            $table->dropColumn('price');
            $table->dropColumn('is_free');
            $table->dropColumn('allowed_unauthorised_user');
        });
    }
}
