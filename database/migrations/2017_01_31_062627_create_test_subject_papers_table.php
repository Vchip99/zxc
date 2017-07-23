<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTestSubjectPapersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('test_subject_papers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('test_category_id')->unsigned();
            $table->integer('test_sub_category_id')->unsigned();
            $table->integer('test_subject_id')->unsigned();
            $table->string('price');
            $table->date('date_to_active');
            $table->char('time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('test_subject_papers');
    }
}
