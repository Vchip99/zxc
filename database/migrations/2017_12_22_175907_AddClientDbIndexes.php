<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddClientDbIndexes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE `client_plans` ADD INDEX idx_client_id_plan_id (`client_id`, `plan_id`)');
        DB::connection('mysql2')->statement('ALTER TABLE `client_assignment_answers` ADD INDEX idx_client_assignment_question_id_client_id (`client_assignment_question_id`, `client_id`)');
        DB::connection('mysql2')->statement('ALTER TABLE `client_assignment_answers` ADD INDEX idx_client_assignment_student_id (`student_id`)');
        DB::connection('mysql2')->statement('ALTER TABLE `client_assignment_questions` ADD INDEX idx_client_assignment_topic_id_client_id (`client_assignment_topic_id`, `client_id`)');
        DB::connection('mysql2')->statement('ALTER TABLE `client_assignment_questions` ADD INDEX idx_client_assignment_subject_id (`client_assignment_subject_id`)');
        DB::connection('mysql2')->statement('ALTER TABLE `client_assignment_topics` ADD INDEX idx_client_assignment_subject_id_client_id (`client_assignment_subject_id`, `client_id`)');
        DB::connection('mysql2')->statement('ALTER TABLE `client_course_comments` ADD INDEX idx_client_online_video_id_client_id (`client_online_video_id`, `client_id`)');
        DB::connection('mysql2')->statement('ALTER TABLE `client_course_comment_likes` ADD INDEX idx_comment_client_online_video_id_client_id (`client_online_video_id`, `client_id`)');
        DB::connection('mysql2')->statement('ALTER TABLE `client_course_comment_likes` ADD INDEX idx_client_course_comment_id (`client_course_comment_id`)');
        DB::connection('mysql2')->statement('ALTER TABLE `client_course_sub_comments` ADD INDEX idx_sub_comment_client_online_video_id_client_id (`client_online_video_id`, `client_id`)');
        DB::connection('mysql2')->statement('ALTER TABLE `client_course_sub_comments` ADD INDEX idx_sub_comment_client_course_comment_id (`client_course_comment_id`)');
        DB::connection('mysql2')->statement('ALTER TABLE `client_course_sub_comment_likes` ADD INDEX idx_sub_comment_likes_client_online_video_id_client_id (`client_online_video_id`, `client_id`)');
        DB::connection('mysql2')->statement('ALTER TABLE `client_course_sub_comment_likes` ADD INDEX idx_sub_comment_likes_client_course_comment_id (`client_course_comment_id`)');
        DB::connection('mysql2')->statement('ALTER TABLE `client_notifications` ADD INDEX idx_created_by_created_to (`created_by`, `created_to`)');
        DB::connection('mysql2')->statement('ALTER TABLE `client_notifications` ADD INDEX idx_notification_client_id (`client_id`)');
        DB::connection('mysql2')->statement('ALTER TABLE `client_online_categories` ADD INDEX idx_client_id (`client_id`)');
        DB::connection('mysql2')->statement('ALTER TABLE `client_online_courses` ADD INDEX idx_category_id_sub_category_id (`category_id`, `sub_category_id`)');
        DB::connection('mysql2')->statement('ALTER TABLE `client_online_courses` ADD INDEX idx_online_courses_client_id (`client_id`)');
        DB::connection('mysql2')->statement('ALTER TABLE `client_online_paper_sections` ADD INDEX idx_section_category_id_sub_category_id (`category_id`, `sub_category_id`)');
        DB::connection('mysql2')->statement('ALTER TABLE `client_online_paper_sections` ADD INDEX idx_section_subject_id_paper_id (`subject_id`, `sub_category_id`)');
        DB::connection('mysql2')->statement('ALTER TABLE `client_online_paper_sections` ADD INDEX idx_section_client_id (`client_id`)');
        DB::connection('mysql2')->statement('ALTER TABLE `client_online_sub_categories` ADD INDEX idx_category_id_client_id (`category_id`, `client_id`)');
        DB::connection('mysql2')->statement('ALTER TABLE `client_online_test_categories` ADD INDEX idx_client_online_test_categories_client_id (`client_id`)');
        DB::connection('mysql2')->statement('ALTER TABLE `client_online_test_questions` ADD INDEX idx_category_id_subcat_id (`category_id`, `subcat_id`)');
        DB::connection('mysql2')->statement('ALTER TABLE `client_online_test_questions` ADD INDEX idx_subject_id_paper_id (`subject_id`, `paper_id`)');
        DB::connection('mysql2')->statement('ALTER TABLE `client_online_test_questions` ADD INDEX idx_question_client_id (`client_id`)');
        DB::connection('mysql2')->statement('ALTER TABLE `client_online_test_sub_categories` ADD INDEX idx_sub_cat_category_id_client_id (`category_id`, `client_id`)');
        DB::connection('mysql2')->statement('ALTER TABLE `client_online_test_subjects` ADD INDEX idx_subject_subject_id_paper_id (`category_id`, `sub_category_id`)');
        DB::connection('mysql2')->statement('ALTER TABLE `client_online_test_subjects` ADD INDEX idx_subject_client_id (`client_id`)');
        DB::connection('mysql2')->statement('ALTER TABLE `client_online_test_subject_papers` ADD INDEX idx_paper_category_id_sub_category_id (`category_id`, `sub_category_id`)');
        DB::connection('mysql2')->statement('ALTER TABLE `client_online_test_subject_papers` ADD INDEX idx_subject_id_client_id (`subject_id`, `client_id`)');
        DB::connection('mysql2')->statement('ALTER TABLE `client_online_videos` ADD INDEX idx_course_id_client_id (`course_id`, `client_id`)');
        DB::connection('mysql2')->statement('ALTER TABLE `client_online_video_likes` ADD INDEX idx_like_client_online_video_id_client_id (`client_online_video_id`, `client_id`)');
        DB::connection('mysql2')->statement('ALTER TABLE `client_read_notifications` ADD INDEX idx_notification_module_created_module_id (`notification_module`, `created_module_id`)');
        DB::connection('mysql2')->statement('ALTER TABLE `client_read_notifications` ADD INDEX idx_client_id_client_user_id (`client_id`, `client_user_id`)');
        DB::connection('mysql2')->statement('ALTER TABLE `client_scores` ADD INDEX idx_score_category_id_subcat_id (`category_id`, `subcat_id`)');
        DB::connection('mysql2')->statement('ALTER TABLE `client_scores` ADD INDEX idx_score_subject_id_paper_id (`subject_id`, `paper_id`)');
        DB::connection('mysql2')->statement('ALTER TABLE `client_user_payments` ADD INDEX idx_client_id_clientuser_id (`client_id`, `clientuser_id`)');
        DB::connection('mysql2')->statement('ALTER TABLE `client_user_purchased_courses` ADD INDEX idx_purchased_course_client_id_user_id (`client_id`, `user_id`)');
        DB::connection('mysql2')->statement('ALTER TABLE `client_user_purchased_test_sub_categories` ADD INDEX idx_purchased_test_sub_category_client_id_user_id (`client_id`, `user_id`)');
        DB::connection('mysql2')->statement('ALTER TABLE `client_user_solutions` ADD INDEX idx_solution_subject_id_paper_id (`subject_id`, `paper_id`)');
        DB::connection('mysql2')->statement('ALTER TABLE `client_user_solutions` ADD INDEX idx_client_user_id_score_id (`client_user_id`, `client_score_id`)');
        DB::connection('mysql2')->statement('ALTER TABLE `register_client_online_courses` ADD INDEX idx_register_course_client_id_user_id (`client_id`, `client_user_id`)');
        DB::connection('mysql2')->statement('ALTER TABLE `register_client_online_papers` ADD INDEX idx_register_paper_client_id_user_id (`client_id`, `client_user_id`)');

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP INDEX `idx_client_id_plan_id` ON `client_plans`');
        DB::connection('mysql2')->statement('DROP INDEX `idx_client_assignment_question_id_client_id` ON `client_assignment_answers`');
        DB::connection('mysql2')->statement('DROP INDEX `idx_client_assignment_student_id` ON `client_assignment_answers`');
        DB::connection('mysql2')->statement('DROP INDEX `idx_client_assignment_topic_id_client_id` ON `client_assignment_questions`');
        DB::connection('mysql2')->statement('DROP INDEX `idx_client_assignment_subject_id` ON `client_assignment_questions`');
        DB::connection('mysql2')->statement('DROP INDEX `idx_client_assignment_subject_id_client_id` ON `client_assignment_topics`');
        DB::connection('mysql2')->statement('DROP INDEX `idx_client_online_video_id_client_id` ON `client_course_comments`');
        DB::connection('mysql2')->statement('DROP INDEX `idx_comment_client_online_video_id_client_id` ON `client_course_comment_likes`');
        DB::connection('mysql2')->statement('DROP INDEX `idx_client_course_comment_id` ON `client_course_comment_likes`');
        DB::connection('mysql2')->statement('DROP INDEX `idx_sub_comment_client_online_video_id_client_id` ON `client_course_sub_comments`');
        DB::connection('mysql2')->statement('DROP INDEX `idx_sub_comment_client_course_comment_id` ON `client_course_sub_comments`');
        DB::connection('mysql2')->statement('DROP INDEX `idx_sub_comment_likes_client_online_video_id_client_id` ON `client_course_sub_comment_likes`');
        DB::connection('mysql2')->statement('DROP INDEX `idx_sub_comment_likes_client_course_comment_id` ON `client_course_sub_comment_likes`');
        DB::connection('mysql2')->statement('DROP INDEX `idx_created_by_created_to` ON `client_notifications`');
        DB::connection('mysql2')->statement('DROP INDEX `idx_notification_client_id` ON `client_notifications`');
        DB::connection('mysql2')->statement('DROP INDEX `idx_client_id` ON `client_online_categories`');
        DB::connection('mysql2')->statement('DROP INDEX `idx_category_id_sub_category_id` ON `client_online_courses`');
        DB::connection('mysql2')->statement('DROP INDEX `idx_online_courses_client_id` ON `client_online_courses`');
        DB::connection('mysql2')->statement('DROP INDEX `idx_section_category_id_sub_category_id` ON `client_online_paper_sections`');
        DB::connection('mysql2')->statement('DROP INDEX `idx_section_subject_id_paper_id` ON `client_online_paper_sections`');
        DB::connection('mysql2')->statement('DROP INDEX `idx_section_client_id` ON `client_online_paper_sections`');
        DB::connection('mysql2')->statement('DROP INDEX `idx_category_id_client_id` ON `client_online_sub_categories`');
        DB::connection('mysql2')->statement('DROP INDEX `idx_client_online_test_categories_client_id` ON `client_online_test_categories`');
        DB::connection('mysql2')->statement('DROP INDEX `idx_category_id_subcat_id` ON `client_online_test_questions`');
        DB::connection('mysql2')->statement('DROP INDEX `idx_subject_id_paper_id` ON `client_online_test_questions`');
        DB::connection('mysql2')->statement('DROP INDEX `idx_question_client_id` ON `client_online_test_questions`');
        DB::connection('mysql2')->statement('DROP INDEX `idx_sub_cat_category_id_client_id` ON `client_online_test_sub_categories`');
        DB::connection('mysql2')->statement('DROP INDEX `idx_subject_subject_id_paper_id` ON `client_online_test_subjects`');
        DB::connection('mysql2')->statement('DROP INDEX `idx_subject_client_id` ON `client_online_test_subjects`');
        DB::connection('mysql2')->statement('DROP INDEX `idx_paper_category_id_sub_category_id` ON `client_online_test_subject_papers`');
        DB::connection('mysql2')->statement('DROP INDEX `idx_subject_id_client_id` ON `client_online_test_subject_papers`');
        DB::connection('mysql2')->statement('DROP INDEX `idx_course_id_client_id` ON `client_online_videos`');
        DB::connection('mysql2')->statement('DROP INDEX `idx_like_client_online_video_id_client_id` ON `client_online_video_likes`');
        DB::connection('mysql2')->statement('DROP INDEX `idx_notification_module_created_module_id` ON `client_read_notifications`');
        DB::connection('mysql2')->statement('DROP INDEX `idx_client_id_client_user_id` ON `client_read_notifications`');
        DB::connection('mysql2')->statement('DROP INDEX `idx_score_category_id_subcat_id` ON `client_scores`');
        DB::connection('mysql2')->statement('DROP INDEX `idx_score_subject_id_paper_id` ON `client_scores`');
        DB::connection('mysql2')->statement('DROP INDEX `idx_client_id_clientuser_id` ON `client_user_payments`');
        DB::connection('mysql2')->statement('DROP INDEX `idx_purchased_course_client_id_user_id` ON `client_user_purchased_courses`');
        DB::connection('mysql2')->statement('DROP INDEX `idx_purchased_test_sub_category_client_id_user_id` ON `client_user_purchased_test_sub_categories`');
        DB::connection('mysql2')->statement('DROP INDEX `idx_solution_subject_id_paper_id` ON `client_user_solutions`');
        DB::connection('mysql2')->statement('DROP INDEX `idx_client_user_id_score_id` ON `client_user_solutions`');
        DB::connection('mysql2')->statement('DROP INDEX `idx_register_course_client_id_user_id` ON `register_client_online_courses`');
        DB::connection('mysql2')->statement('DROP INDEX `idx_register_paper_client_id_user_id` ON `register_client_online_papers`');
    }
}
