<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientHomePagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql2')->create('client_home_pages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('client_id');
            $table->string('subdomain');
            $table->text('home_content_value');
            $table->text('background_image');
            $table->text('home_about_value');
            $table->text('home_about_content');
            $table->tinyInteger('about_show_hide');
            $table->text('home_vission_content');
            $table->text('home_mission_content');
            $table->text('home_course_name');
            $table->text('home_course_content');
            $table->tinyInteger('course_show_hide');
            $table->text('home_test_value');
            $table->text('home_test_content');
            $table->tinyInteger('test_show_hide');
            $table->tinyInteger('customer_show_hide');
            $table->text('home_customer_value');
            $table->text('home_customer_content');
            $table->tinyInteger('testimonial_show_hide');
            $table->tinyInteger('team_show_hide');
            $table->text('contact_us');
            $table->string('institute_name');
            $table->string('institute_url');
            $table->string('facebook_url');
            $table->string('twitter_url');
            $table->string('google_url');
            $table->string('linkedin_url');
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
        Schema::connection('mysql2')->drop('client_home_pages');
    }
}
