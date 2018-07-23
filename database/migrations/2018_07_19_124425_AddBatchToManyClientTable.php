<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBatchToManyClientTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql2')->table('client_assignment_subjects', function (Blueprint $table) {
            $table->integer('client_batch_id')->default(0);
        });
        Schema::connection('mysql2')->table('client_assignment_topics', function (Blueprint $table) {
            $table->integer('client_batch_id')->default(0);
        });
        Schema::connection('mysql2')->table('client_assignment_questions', function (Blueprint $table) {
            $table->integer('client_batch_id')->default(0);
        });
        Schema::connection('mysql2')->table('clientusers', function (Blueprint $table) {
            $table->integer('unchecked_assignments')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql2')->table('client_assignment_subjects', function (Blueprint $table) {
            $table->dropColumn('client_batch_id');
        });
        Schema::connection('mysql2')->table('client_assignment_topics', function (Blueprint $table) {
            $table->dropColumn('client_batch_id');
        });
        Schema::connection('mysql2')->table('client_assignment_questions', function (Blueprint $table) {
            $table->dropColumn('client_batch_id');
        });
        Schema::connection('mysql2')->table('clientusers', function (Blueprint $table) {
            $table->integer('unchecked_assignments')->nullable();
        });
    }
}
