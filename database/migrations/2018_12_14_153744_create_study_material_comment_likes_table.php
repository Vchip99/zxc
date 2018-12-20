<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudyMaterialCommentLikesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('study_material_comment_likes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('study_material_topic_id')->unsigned();
            $table->integer('study_material_post_id')->unsigned();
            $table->integer('study_material_comment_id')->unsigned();
            $table->integer('user_id')->unsigned();
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
        Schema::drop('study_material_comment_likes');
    }
}
