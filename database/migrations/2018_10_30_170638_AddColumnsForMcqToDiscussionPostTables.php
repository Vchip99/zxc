<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsForMcqToDiscussionPostTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('discussion_posts', function (Blueprint $table) {
            $table->string('answer1');
            $table->string('answer2');
            $table->string('answer3');
            $table->string('answer4');
            $table->string('answer');
            $table->text('solution');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('discussion_posts', function (Blueprint $table) {
            $table->dropColumn('answer1');
            $table->dropColumn('answer2');
            $table->dropColumn('answer3');
            $table->dropColumn('answer4');
            $table->dropColumn('answer');
            $table->dropColumn('solution');
        });
    }
}
