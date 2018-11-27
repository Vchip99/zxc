<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCreatedByColumnToClientTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql2')->table('client_online_categories', function (Blueprint $table) {
           $table->integer('created_by')->default(0);
        });
        Schema::connection('mysql2')->table('client_online_sub_categories', function (Blueprint $table) {
           $table->integer('created_by')->default(0);
        });
        Schema::connection('mysql2')->table('client_online_courses', function (Blueprint $table) {
           $table->integer('created_by')->default(0);
        });
        Schema::connection('mysql2')->table('client_online_videos', function (Blueprint $table) {
           $table->integer('created_by')->default(0);
        });
        Schema::connection('mysql2')->table('client_online_test_categories', function (Blueprint $table) {
           $table->integer('created_by')->default(0);
        });
        Schema::connection('mysql2')->table('client_online_test_sub_categories', function (Blueprint $table) {
           $table->integer('created_by')->default(0);
        });
        Schema::connection('mysql2')->table('client_online_test_subjects', function (Blueprint $table) {
           $table->integer('created_by')->default(0);
        });
        Schema::connection('mysql2')->table('client_online_test_subject_papers', function (Blueprint $table) {
           $table->integer('created_by')->default(0);
        });
        Schema::connection('mysql2')->table('client_online_paper_sections', function (Blueprint $table) {
           $table->integer('created_by')->default(0);
        });
        Schema::connection('mysql2')->table('client_online_test_questions', function (Blueprint $table) {
           $table->integer('created_by')->default(0);
        });
        Schema::connection('mysql2')->table('client_batches', function (Blueprint $table) {
           $table->integer('created_by')->default(0);
        });
        Schema::connection('mysql2')->table('client_user_attendances', function (Blueprint $table) {
           $table->integer('created_by')->default(0);
        });
        Schema::connection('mysql2')->table('client_offline_paper_marks', function (Blueprint $table) {
           $table->integer('created_by')->default(0);
        });
        Schema::connection('mysql2')->table('client_assignment_subjects', function (Blueprint $table) {
           $table->integer('created_by')->default(0);
        });
        Schema::connection('mysql2')->table('client_assignment_topics', function (Blueprint $table) {
           $table->integer('created_by')->default(0);
        });
        Schema::connection('mysql2')->table('client_assignment_questions', function (Blueprint $table) {
           $table->integer('created_by')->default(0);
        });
        Schema::connection('mysql2')->table('client_messages', function (Blueprint $table) {
           $table->integer('created_by')->default(0);
        });
        Schema::connection('mysql2')->table('client_assignment_answers', function (Blueprint $table) {
           $table->integer('created_by')->default(0);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql2')->table('client_online_categories', function (Blueprint $table) {
           $table->dropColumn('created_by');
        });
        Schema::connection('mysql2')->table('client_online_sub_categories', function (Blueprint $table) {
           $table->dropColumn('created_by');
        });
        Schema::connection('mysql2')->table('client_online_courses', function (Blueprint $table) {
           $table->dropColumn('created_by');
        });
        Schema::connection('mysql2')->table('client_online_videos', function (Blueprint $table) {
           $table->dropColumn('created_by');
        });
        Schema::connection('mysql2')->table('client_online_test_categories', function (Blueprint $table) {
           $table->dropColumn('created_by');
        });
        Schema::connection('mysql2')->table('client_online_test_sub_categories', function (Blueprint $table) {
           $table->dropColumn('created_by');
        });
        Schema::connection('mysql2')->table('client_online_test_subjects', function (Blueprint $table) {
           $table->dropColumn('created_by');
        });
        Schema::connection('mysql2')->table('client_online_test_subject_papers', function (Blueprint $table) {
           $table->dropColumn('created_by');
        });
        Schema::connection('mysql2')->table('client_online_paper_sections', function (Blueprint $table) {
           $table->dropColumn('created_by');
        });
        Schema::connection('mysql2')->table('client_online_test_questions', function (Blueprint $table) {
           $table->dropColumn('created_by');
        });
        Schema::connection('mysql2')->table('client_batches', function (Blueprint $table) {
           $table->dropColumn('created_by');
        });
        Schema::connection('mysql2')->table('client_user_attendances', function (Blueprint $table) {
           $table->dropColumn('created_by');
        });
        Schema::connection('mysql2')->table('client_offline_paper_marks', function (Blueprint $table) {
           $table->dropColumn('created_by');
        });
        Schema::connection('mysql2')->table('client_assignment_subjects', function (Blueprint $table) {
           $table->dropColumn('created_by');
        });
        Schema::connection('mysql2')->table('client_assignment_topics', function (Blueprint $table) {
           $table->dropColumn('created_by');
        });
        Schema::connection('mysql2')->table('client_assignment_questions', function (Blueprint $table) {
           $table->dropColumn('created_by');
        });
        Schema::connection('mysql2')->table('client_messages', function (Blueprint $table) {
           $table->dropColumn('created_by');
        });
        Schema::connection('mysql2')->table('client_assignment_answers', function (Blueprint $table) {
           $table->dropColumn('created_by');
        });
    }
}
