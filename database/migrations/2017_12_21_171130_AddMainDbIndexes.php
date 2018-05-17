<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMainDbIndexes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE `advertisement_pages` ADD INDEX idx_parent_page (`parent_page`)');
        DB::statement('ALTER TABLE `adds` ADD INDEX idx_show_page_id (`show_page_id`)');
        DB::statement('ALTER TABLE `adds` ADD INDEX idx_start_date_end_date (`start_date`, `end_date`)');
        DB::statement('ALTER TABLE `assignment_answers` ADD INDEX idx_student_id (`student_id`)');
        DB::statement('ALTER TABLE `assignment_answers` ADD INDEX idx_lecturer_id (`lecturer_id`)');
        DB::statement('ALTER TABLE `assignment_questions` ADD INDEX idx_college_id_college_dept_id (`college_id`,`college_dept_id`)');
        DB::statement('ALTER TABLE `assignment_questions` ADD INDEX idx_lecturer_id (`lecturer_id`)');
        DB::statement('ALTER TABLE `blog_comments` ADD INDEX idx_blog_id (`blog_id`)');
        DB::statement('ALTER TABLE `blog_comments` ADD INDEX idx_user_id (`user_id`)');
        DB::statement('ALTER TABLE `blog_comment_likes` ADD INDEX idx_blog_id_blog_comment_id (`blog_id`, `blog_comment_id`)');
        DB::statement('ALTER TABLE `blog_sub_comments` ADD INDEX idx_blog_id_blog_comment_id (`blog_id`, `blog_comment_id`)');
        DB::statement('ALTER TABLE `blog_sub_comment_likes` ADD INDEX idx_blog_id_comment_id_sub_comment_id (`blog_id`, `blog_comment_id`, `blog_sub_comment_id`)');
        DB::statement('ALTER TABLE `course_comments` ADD INDEX idx_course_video_id (`course_video_id`)');
        DB::statement('ALTER TABLE `course_comment_likes` ADD INDEX idx_course_video_id_course_comment_id (`course_video_id`, `course_comment_id`)');
        DB::statement('ALTER TABLE `course_courses` ADD INDEX idx_course_category_id_sub_category_id (`course_category_id`, `course_sub_category_id`)');
        DB::statement('ALTER TABLE `course_sub_categories` ADD INDEX idx_course_category_id (`course_category_id`)');
        DB::statement('ALTER TABLE `course_sub_comments` ADD INDEX idx_course_video_id_comment_id (`course_video_id`, `course_comment_id`)');
        DB::statement('ALTER TABLE `course_sub_comment_likes` ADD INDEX idx_course_video_id_comment_id_sub_comment_id (`course_video_id`, `course_comment_id`, `course_sub_comment_id`)');
        DB::statement('ALTER TABLE `course_videos` ADD INDEX idx_course_id (`course_id`)');
        DB::statement('ALTER TABLE `course_video_likes` ADD INDEX idx_course_video_id (`course_video_id`)');
        DB::statement('ALTER TABLE `discussion_comments` ADD INDEX idx_discussion_post_id (`discussion_post_id`)');
        DB::statement('ALTER TABLE `discussion_comment_likes` ADD INDEX idx_discussion_post_id_comment_id (`discussion_post_id`, `discussion_comment_id`)');
        DB::statement('ALTER TABLE `discussion_post_likes` ADD INDEX idx_discussion_post_id (`discussion_post_id`)');
        DB::statement('ALTER TABLE `discussion_sub_comments` ADD INDEX idx_discussion_post_id_comment_id (`discussion_post_id`, `discussion_comment_id`)');
        DB::statement('ALTER TABLE `discussion_sub_comment_likes` ADD INDEX idx_discussion_post_id_comment_id_sub_comment_id (`discussion_post_id`, `discussion_comment_id`,`discussion_sub_comment_id`)');
        DB::statement('ALTER TABLE `live_course_comments` ADD INDEX idx_live_course_video_id (`live_course_video_id`)');
        DB::statement('ALTER TABLE `live_course_comment_likes` ADD INDEX idx_live_course_video_id_comment_id (`live_course_video_id`, `live_course_comment_id`)');
        DB::statement('ALTER TABLE `live_course_sub_comments` ADD INDEX idx_sub_comment_live_course_video_id_comment_id (`live_course_video_id`, `live_course_comment_id`)');
        DB::statement('ALTER TABLE `live_course_sub_comment_likes` ADD INDEX idx_live_course_video_id_comment_id_sub_comment_id (`live_course_video_id`, `live_course_comment_id`,`live_course_sub_comment_id`)');
        DB::statement('ALTER TABLE `notifications` ADD INDEX idx_created_by_created_to (`created_by`, `created_to`)');
        DB::statement('ALTER TABLE `paper_sections` ADD INDEX idx_test_subject_id_paper_id (`test_subject_id`, `test_subject_paper_id`)');
        DB::statement('ALTER TABLE `questions` ADD INDEX idx_category_id_subcat_id (`category_id`, `subcat_id`)');
        DB::statement('ALTER TABLE `questions` ADD INDEX idx_subject_id_paper_id (`subject_id`, `paper_id`)');
        DB::statement('ALTER TABLE `scores` ADD INDEX idx_category_id_subcat_id (`category_id`, `subcat_id`)');
        DB::statement('ALTER TABLE `scores` ADD INDEX idx_subject_id_paper_id (`subject_id`, `paper_id`)');
        DB::statement('ALTER TABLE `test_subjects` ADD INDEX idx_test_category_id_sub_category_id (`test_category_id`, `test_sub_category_id`)');
        DB::statement('ALTER TABLE `test_subject_papers` ADD INDEX idx_paper_test_category_id_sub_category_id (`test_category_id`, `test_sub_category_id`)');
        DB::statement('ALTER TABLE `test_subject_papers` ADD INDEX idx_test_subject_id (`test_subject_id`)');
        DB::statement('ALTER TABLE `vkit_project_comments` ADD INDEX idx_vkit_project_id (`vkit_project_id`)');
        DB::statement('ALTER TABLE `vkit_project_comment_likes` ADD INDEX idx_vkit_project_id_comment_id (`vkit_project_id`, `vkit_project_comment_id`)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP INDEX `idx_parent_page` ON `advertisement_pages`');
        DB::statement('DROP INDEX `idx_show_page_id` ON `adds`');
        DB::statement('DROP INDEX `idx_start_date_end_date` ON `adds`');
        DB::statement('DROP INDEX `idx_student_id` ON `assignment_answers`');
        DB::statement('DROP INDEX `idx_lecturer_id` ON `assignment_answers`');
        DB::statement('DROP INDEX `idx_college_id_college_dept_id` ON `assignment_questions`');
        DB::statement('DROP INDEX `idx_lecturer_id` ON `assignment_questions`');
        DB::statement('DROP INDEX `idx_blog_id` ON `blog_comments`');
        DB::statement('DROP INDEX `idx_user_id` ON `blog_comments`');
        DB::statement('DROP INDEX `idx_blog_id_blog_comment_id` ON `blog_comment_likes`');
        DB::statement('DROP INDEX `idx_blog_id_blog_comment_id` ON `blog_sub_comments`');
        DB::statement('DROP INDEX `idx_blog_id_comment_id_sub_comment_id` ON `blog_sub_comment_likes`');
        DB::statement('DROP INDEX `idx_course_video_id` ON `course_comments`');
        DB::statement('DROP INDEX `idx_course_video_id_course_comment_id` ON `course_comment_likes`');
        DB::statement('DROP INDEX `idx_course_category_id_sub_category_id` ON `course_courses`');
        DB::statement('DROP INDEX `idx_course_category_id` ON `course_sub_categories`');
        DB::statement('DROP INDEX `idx_course_video_id_comment_id` ON `course_sub_comments`');
        DB::statement('DROP INDEX `idx_course_video_id_comment_id_sub_comment_id` ON `course_sub_comment_likes`');
        DB::statement('DROP INDEX `idx_course_id` ON `course_videos`');
        DB::statement('DROP INDEX `idx_course_video_id` ON `course_video_likes`');
        DB::statement('DROP INDEX `idx_discussion_post_id` ON `discussion_comments`');
        DB::statement('DROP INDEX `idx_discussion_post_id_comment_id` ON `discussion_comment_likes`');
        DB::statement('DROP INDEX `idx_discussion_post_id` ON `discussion_post_likes`');
        DB::statement('DROP INDEX `idx_discussion_post_id_comment_id` ON `discussion_sub_comments`');
        DB::statement('DROP INDEX `idx_discussion_post_id_comment_id_sub_comment_id` ON `discussion_sub_comment_likes`');
        DB::statement('DROP INDEX `idx_live_course_video_id` ON `live_course_comments`');
        DB::statement('DROP INDEX `idx_live_course_video_id_comment_id` ON `live_course_comment_likes`');
        DB::statement('DROP INDEX `idx_sub_comment_live_course_video_id_comment_id` ON `live_course_sub_comments`');
        DB::statement('DROP INDEX `idx_live_course_video_id_comment_id_sub_comment_id` ON `live_course_sub_comment_likes`');
        DB::statement('DROP INDEX `idx_created_by_created_to` ON `notifications`');
        DB::statement('DROP INDEX `idx_test_subject_id_paper_id` ON `paper_sections`');
        DB::statement('DROP INDEX `idx_category_id_subcat_id` ON `questions`');
        DB::statement('DROP INDEX `idx_subject_id_paper_id` ON `questions`');
        DB::statement('DROP INDEX `idx_category_id_subcat_id` ON `scores`');
        DB::statement('DROP INDEX `idx_subject_id_paper_id` ON `scores`');
        DB::statement('DROP INDEX `idx_test_category_id_sub_category_id` ON `test_subjects`');
        DB::statement('DROP INDEX `idx_paper_test_category_id_sub_category_id` ON `test_subject_papers`');
        DB::statement('DROP INDEX `idx_test_subject_id` ON `test_subject_papers`');
        DB::statement('DROP INDEX `idx_vkit_project_id` ON `vkit_project_comments`');
        DB::statement('DROP INDEX `idx_vkit_project_id_comment_id` ON `vkit_project_comment_likes`');
    }
}
