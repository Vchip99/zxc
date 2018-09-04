<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddParentColumnsToClientUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql2')->table('clientusers', function (Blueprint $table) {
            $table->string('parent_name')->nullable();
            $table->string('parent_phone')->nullable();
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
            $table->dropColumn('parent_name');
            $table->dropColumn('parent_phone');
        });
    }
}
