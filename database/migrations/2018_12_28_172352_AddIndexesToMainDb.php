<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexesToMainDb extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE `advertisements` ADD INDEX `idx_admin_id` (`admin_id`)');
        DB::statement('ALTER TABLE `assignment_topics` ADD INDEX `idx_college_subject_id` (`college_subject_id`)');
        DB::statement('ALTER TABLE `assignment_topics` ADD INDEX `ixd_lecturer_id` (`lecturer_id`)');
        DB::statement('ALTER TABLE `assignment_topics` ADD INDEX `idx_college_id` (`college_id`)');
        DB::statement('ALTER TABLE `assignment_topics` ADD INDEX `idx_lecturer_type` (`lecturer_type`)');
        DB::statement('ALTER TABLE `chat_messages` ADD INDEX `idx_sender_id` (`sender_id`)');
        DB::statement('ALTER TABLE `chat_messages` ADD INDEX `idx_receiver_id` (`receiver_id`)');
        DB::statement('ALTER TABLE `college_categories` ADD INDEX `idx_college_id` (`college_id`)');
        DB::statement('ALTER TABLE `college_categories` ADD INDEX `idx_college_dept_id` (`college_dept_id`)');
        DB::statement('ALTER TABLE `college_categories` ADD INDEX `idx_user_id` (`user_id`)');
        DB::statement('ALTER TABLE `college_class_exams` ADD INDEX `idx_college_id` (`college_id`)');
        DB::statement('ALTER TABLE `college_class_exams` ADD INDEX `idx_college_subject_id` (`college_subject_id`)');
        DB::statement('ALTER TABLE `college_depts` ADD INDEX `idx_college_id` (`college_id`)');
        DB::statement('ALTER TABLE `college_extra_classes` ADD INDEX `idx_college_id` (`college_id`)');
        DB::statement('ALTER TABLE `college_gallery_images` ADD INDEX `idx_college_id` (`college_id`)');
        DB::statement('ALTER TABLE `college_gallery_types` ADD INDEX `idx_college_id` (`college_id`)');
        DB::statement('ALTER TABLE `college_holidays` ADD INDEX `idx_college_id` (`college_id`)');
        DB::statement('ALTER TABLE `college_individual_messages` ADD INDEX `idx_college_id` (`college_id`)');
        DB::statement('ALTER TABLE `college_individual_messages` ADD INDEX `idx_college_dept_id` (`college_dept_id`)');
        DB::statement('ALTER TABLE `college_individual_messages` ADD INDEX `idx_year` (`year`)');
        DB::statement('ALTER TABLE `college_messages` ADD INDEX `idx_college_id` (`college_id`)');
        DB::statement('ALTER TABLE `college_notices` ADD INDEX `idx_college_id` (`college_id`)');
        DB::statement('ALTER TABLE `college_offline_paper_marks` ADD INDEX `idx_college_id` (`college_id`)');
        DB::statement('ALTER TABLE `college_offline_paper_marks` ADD INDEX `idx_college_subject_id` (`college_subject_id`)');
        DB::statement('ALTER TABLE `college_payments` ADD INDEX `idx_college_id` (`college_id`)');
        DB::statement('ALTER TABLE `college_payments` ADD INDEX `idx_user_id` (`user_id`)');
        DB::statement('ALTER TABLE `college_subjects` ADD INDEX `idx_college_id` (`college_id`)');
        DB::statement('ALTER TABLE `college_time_tables` ADD INDEX `idx_college_id` (`college_id`)');
        DB::statement('ALTER TABLE `college_time_tables` ADD INDEX `idx_college_dept_id` (`college_dept_id`)');
        DB::statement('ALTER TABLE `college_time_tables` ADD INDEX `idx_year` (`year`)');
        DB::statement('ALTER TABLE `college_user_attendances` ADD INDEX `idx_college_id` (`college_id`)');
        DB::statement('ALTER TABLE `college_user_attendances` ADD INDEX `idx_college_dept_id` (`college_dept_id`)');
        DB::statement('ALTER TABLE `college_user_attendances` ADD INDEX `idx_year` (`year`)');
        DB::statement('ALTER TABLE `college_user_attendances` ADD INDEX `idx_college_subject_id` (`college_subject_id`)');
        DB::statement('ALTER TABLE `discussion_posts` ADD INDEX `idx_category_id` (`category_id`)');
        DB::statement('ALTER TABLE `discussion_posts` ADD INDEX `idx_user_id` (`user_id`)');
        DB::statement('ALTER TABLE `exam_patterns` ADD INDEX `idx_placement_area_id` (`placement_area_id`)');
        DB::statement('ALTER TABLE `exam_patterns` ADD INDEX `idx_placement_company_id` (`placement_company_id`)');
        DB::statement('ALTER TABLE `mentors` ADD INDEX `idx_mentor_area_id` (`mentor_area_id`)');
        DB::statement('ALTER TABLE `mentor_chat_messages` ADD INDEX `idx_mentor_chat_room_id` (`mentor_chat_room_id`)');
        DB::statement('ALTER TABLE `mentor_chat_messages` ADD INDEX `idx_sender_id` (`sender_id`)');
        DB::statement('ALTER TABLE `mentor_chat_messages` ADD INDEX `idx_receiver_id` (`receiver_id`)');
        DB::statement('ALTER TABLE `mentor_ratings` ADD INDEX `idx_mentor_id` (`mentor_id`)');
        DB::statement('ALTER TABLE `mentor_ratings` ADD INDEX `idx_user_id` (`user_id`)');
        DB::statement('ALTER TABLE `mentor_schedules` ADD INDEX `idx_mentor_id` (`mentor_id`)');
        DB::statement('ALTER TABLE `mentor_schedules` ADD INDEX `idx_user_id` (`user_id`)');
        DB::statement('ALTER TABLE `mentor_skills` ADD INDEX `idx_mentor_area_id` (`mentor_area_id`)');
        DB::statement('ALTER TABLE `mock_interview_reviews` ADD INDEX `idx_user_data_id` (`user_data_id`)');
        DB::statement('ALTER TABLE `mock_interview_reviews` ADD INDEX `idx_user_id` (`user_id`)');
        DB::statement('ALTER TABLE `payments` ADD INDEX `idx_client_plan_id` (`client_plan_id`)');
        DB::statement('ALTER TABLE `placement_companies` ADD INDEX `idx_placement_area_id` (`placement_area_id`)');
        DB::statement('ALTER TABLE `placement_experiances` ADD INDEX `idx_placement_area_id` (`placement_area_id`)');
        DB::statement('ALTER TABLE `placement_experiances` ADD INDEX `idx_placement_company_id` (`placement_company_id`)');
        DB::statement('ALTER TABLE `placement_faqs` ADD INDEX `idx_placement_area_id` (`placement_area_id`)');
        DB::statement('ALTER TABLE `placement_faqs` ADD INDEX `idx_placement_company_id` (`placement_company_id`)');
        DB::statement('ALTER TABLE `placement_processes` ADD INDEX `idx_placement_area_id` (`placement_area_id`)');
        DB::statement('ALTER TABLE `placement_processes` ADD INDEX `idx_placement_company_id` (`placement_company_id`)');
        DB::statement('ALTER TABLE `placement_process_comments` ADD INDEX `idx_company_id` (`company_id`)');
        DB::statement('ALTER TABLE `placement_process_comments` ADD INDEX `idx_user_id` (`user_id`)');
        DB::statement('ALTER TABLE `placement_process_comment_likes` ADD INDEX `idx_company_id` (`company_id`)');
        DB::statement('ALTER TABLE `placement_process_comment_likes` ADD INDEX `idx_user_id` (`user_id`)');
        DB::statement('ALTER TABLE `placement_process_comment_likes` ADD INDEX `idx_placement_process_comment_id` (`placement_process_comment_id`)');
        DB::statement('ALTER TABLE `placement_process_likes` ADD INDEX `idx_company_id` (`company_id`)');
        DB::statement('ALTER TABLE `placement_process_likes` ADD INDEX `idx_user_id` (`user_id`)');
        DB::statement('ALTER TABLE `placement_process_sub_comments` ADD INDEX `idx_company_id` (`company_id`)');
        DB::statement('ALTER TABLE `placement_process_sub_comments` ADD INDEX `idx_user_id` (`user_id`)');
        DB::statement('ALTER TABLE `placement_process_sub_comments` ADD INDEX `idx_placement_process_comment_id` (`placement_process_comment_id`)');
        DB::statement('ALTER TABLE `placement_process_sub_comment_likes` ADD INDEX `idx_company_id` (`company_id`)');
        DB::statement('ALTER TABLE `placement_process_sub_comment_likes` ADD INDEX `idx_user_id` (`user_id`)');
        DB::statement('ALTER TABLE `placement_process_sub_comment_likes` ADD INDEX `idx_placement_process_comment_id` (`placement_process_comment_id`)');
        DB::statement('ALTER TABLE `placement_process_sub_comment_likes` ADD INDEX `idx_placement_process_sub_comment_id` (`placement_process_sub_comment_id`)');
        DB::statement('ALTER TABLE `question_bank_questions` ADD INDEX `idx_category_id_subcat_id` (`category_id`, `subcat_id`)');
        DB::statement('ALTER TABLE `question_bank_sub_categories` ADD INDEX `idx_question_bank_category_id` (`question_bank_category_id`)');
        DB::statement('ALTER TABLE `ratings` ADD INDEX `idx_module_id` (`module_id`)');
        DB::statement('ALTER TABLE `ratings` ADD INDEX `idx_user_id` (`user_id`)');
        DB::statement('ALTER TABLE `ratings` ADD INDEX `idx_module_type` (`module_type`)');
        DB::statement('ALTER TABLE `read_notifications` ADD INDEX `idx_notification_id` (`notification_id`)');
        DB::statement('ALTER TABLE `read_notifications` ADD INDEX `idx_notification_module` (`notification_module`)');
        DB::statement('ALTER TABLE `read_notifications` ADD INDEX `idx_user_id` (`user_id`)');
        DB::statement('ALTER TABLE `read_notifications` ADD INDEX `idx_created_module_id` (`created_module_id`)');
        DB::statement('ALTER TABLE `register_documents` ADD INDEX `idx_user_id` (`user_id`)');
        DB::statement('ALTER TABLE `register_documents` ADD INDEX `idx_documents_docs_id` (`documents_docs_id`)');
        DB::statement('ALTER TABLE `register_favourite_documents` ADD INDEX `idx_user_id` (`user_id`)');
        DB::statement('ALTER TABLE `register_favourite_documents` ADD INDEX `idx_documents_docs_id` (`documents_docs_id`)');
        DB::statement('ALTER TABLE `register_online_courses` ADD INDEX `idx_user_id` (`user_id`)');
        DB::statement('ALTER TABLE `register_online_courses` ADD INDEX `idx_online_course_id` (`online_course_id`)');
        DB::statement('ALTER TABLE `register_papers` ADD INDEX `idx_user_id` (`user_id`)');
        DB::statement('ALTER TABLE `register_papers` ADD INDEX `idx_test_subject_paper_id` (`test_subject_paper_id`)');
        DB::statement('ALTER TABLE `register_projects` ADD INDEX `idx_user_id` (`user_id`)');
        DB::statement('ALTER TABLE `register_projects` ADD INDEX `idx_project_id` (`project_id`)');
        DB::statement('ALTER TABLE `study_material_comments` ADD INDEX `idx_study_material_topic_id` (`study_material_topic_id`)');
        DB::statement('ALTER TABLE `study_material_comments` ADD INDEX `idx_study_material_post_id` (`study_material_post_id`)');
        DB::statement('ALTER TABLE `study_material_comments` ADD INDEX `idx_user_id` (`user_id`)');
        DB::statement('ALTER TABLE `study_material_comment_likes` ADD INDEX `idx_study_material_topic_id` (`study_material_topic_id`)');
        DB::statement('ALTER TABLE `study_material_comment_likes` ADD INDEX `idx_study_material_post_id` (`study_material_post_id`)');
        DB::statement('ALTER TABLE `study_material_comment_likes` ADD INDEX `idx_study_material_comment_id` (`study_material_comment_id`)');
        DB::statement('ALTER TABLE `study_material_comment_likes` ADD INDEX `idx_user_id` (`user_id`)');
        DB::statement('ALTER TABLE `study_material_posts` ADD INDEX `idx_course_category_id` (`course_category_id`)');
        DB::statement('ALTER TABLE `study_material_posts` ADD INDEX `idx_course_sub_category_id` (`course_sub_category_id`)');
        DB::statement('ALTER TABLE `study_material_posts` ADD INDEX `idx_study_material_subject_id` (`study_material_subject_id`)');
        DB::statement('ALTER TABLE `study_material_posts` ADD INDEX `idx_study_material_topic_id` (`study_material_topic_id`)');

        DB::statement('ALTER TABLE `study_material_post_likes` ADD INDEX `idx_study_material_topic_id` (`study_material_topic_id`)');
        DB::statement('ALTER TABLE `study_material_post_likes` ADD INDEX `idx_study_material_post_id` (`study_material_post_id`)');
        DB::statement('ALTER TABLE `study_material_post_likes` ADD INDEX `idx_user_id` (`user_id`)');

        DB::statement('ALTER TABLE `study_material_subjects` ADD INDEX `idx_course_category_id` (`course_category_id`)');
        DB::statement('ALTER TABLE `study_material_subjects` ADD INDEX `idx_course_sub_category_id` (`course_sub_category_id`)');
        DB::statement('ALTER TABLE `study_material_subjects` ADD INDEX `idx_admin_id` (`admin_id`)');

        DB::statement('ALTER TABLE `study_material_sub_comments` ADD INDEX `idx_study_material_topic_id` (`study_material_topic_id`)');
        DB::statement('ALTER TABLE `study_material_sub_comments` ADD INDEX `idx_study_material_post_id` (`study_material_post_id`)');
        DB::statement('ALTER TABLE `study_material_sub_comments` ADD INDEX `idx_study_material_comment_id` (`study_material_comment_id`)');
        DB::statement('ALTER TABLE `study_material_sub_comments` ADD INDEX `idx_user_id` (`user_id`)');

        DB::statement('ALTER TABLE `study_material_sub_comment_likes` ADD INDEX `idx_study_material_topic_id` (`study_material_topic_id`)');
        DB::statement('ALTER TABLE `study_material_sub_comment_likes` ADD INDEX `idx_study_material_post_id` (`study_material_post_id`)');
        DB::statement('ALTER TABLE `study_material_sub_comment_likes` ADD INDEX `idx_study_material_comment_id` (`study_material_comment_id`)');
        DB::statement('ALTER TABLE `study_material_sub_comment_likes` ADD INDEX `idx_study_material_sub_comment_id` (`study_material_sub_comment_id`)');
        DB::statement('ALTER TABLE `study_material_sub_comment_likes` ADD INDEX `idx_user_id` (`user_id`)');

        DB::statement('ALTER TABLE `study_material_topics` ADD INDEX `idx_course_category_id` (`course_category_id`)');
        DB::statement('ALTER TABLE `study_material_topics` ADD INDEX `idx_course_sub_category_id` (`course_sub_category_id`)');
        DB::statement('ALTER TABLE `study_material_topics` ADD INDEX `idx_study_material_subject_id` (`study_material_subject_id`)');

        DB::statement('ALTER TABLE `test_sub_categories` ADD INDEX `idx_test_category_id` (`test_category_id`)');


        DB::statement('ALTER TABLE `user_datas` ADD INDEX `idx_category_id` (`category_id`)');
        DB::statement('ALTER TABLE `user_datas` ADD INDEX `idx_sub_category_id` (`sub_category_id`)');
        DB::statement('ALTER TABLE `user_datas` ADD INDEX `idx_subject_id` (`subject_id`)');
        DB::statement('ALTER TABLE `user_datas` ADD INDEX `idx_paper_id` (`paper_id`)');
        DB::statement('ALTER TABLE `user_datas` ADD INDEX `idx_user_id` (`user_id`)');
        DB::statement('ALTER TABLE `user_datas` ADD INDEX `idx_college_id` (`college_id`)');

        DB::statement('ALTER TABLE `user_solutions` ADD INDEX `idx_ques_id` (`ques_id`)');
        DB::statement('ALTER TABLE `user_solutions` ADD INDEX `idx_user_id` (`user_id`)');
        DB::statement('ALTER TABLE `user_solutions` ADD INDEX `idx_paper_id` (`paper_id`)');
        DB::statement('ALTER TABLE `user_solutions` ADD INDEX `idx_subject_id` (`subject_id`)');
        DB::statement('ALTER TABLE `user_solutions` ADD INDEX `idx_score_id` (`score_id`)');

        DB::statement('ALTER TABLE `vkit_projects` ADD INDEX `idx_category_id` (`category_id`)');

        DB::statement('ALTER TABLE `vkit_project_sub_comments` ADD INDEX `idx_vkit_project_id` (`vkit_project_id`)');
        DB::statement('ALTER TABLE `vkit_project_sub_comments` ADD INDEX `idx_vkit_project_comment_id` (`vkit_project_comment_id`)');
        DB::statement('ALTER TABLE `vkit_project_sub_comments` ADD INDEX `idx_user_id` (`user_id`)');

        DB::statement('ALTER TABLE `zero_to_heroes` ADD INDEX `idx_designation_id` (`designation_id`)');
        DB::statement('ALTER TABLE `zero_to_heroes` ADD INDEX `idx_area_id` (`area_id`)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE advertisements DROP INDEX idx_admin_id');
        DB::statement('ALTER TABLE assignment_topics DROP INDEX idx_college_subject_id');
        DB::statement('ALTER TABLE assignment_topics DROP INDEX ixd_lecturer_id');
        DB::statement('ALTER TABLE assignment_topics DROP INDEX idx_college_id');
        DB::statement('ALTER TABLE assignment_topics DROP INDEX idx_lecturer_type');
        DB::statement('ALTER TABLE chat_messages DROP INDEX idx_sender_id');
        DB::statement('ALTER TABLE chat_messages DROP INDEX idx_receiver_id');
        DB::statement('ALTER TABLE college_categories DROP INDEX idx_college_id');
        DB::statement('ALTER TABLE college_categories DROP INDEX idx_college_dept_id');
        DB::statement('ALTER TABLE college_categories DROP INDEX idx_user_id');
        DB::statement('ALTER TABLE college_class_exams DROP INDEX idx_college_id');
        DB::statement('ALTER TABLE college_class_exams DROP INDEX idx_college_subject_id');
        DB::statement('ALTER TABLE college_depts DROP INDEX idx_college_id');
        DB::statement('ALTER TABLE college_extra_classes DROP INDEX idx_college_id');
        DB::statement('ALTER TABLE college_gallery_images DROP INDEX idx_college_id');
        DB::statement('ALTER TABLE college_gallery_types DROP INDEX idx_college_id');
        DB::statement('ALTER TABLE college_holidays DROP INDEX idx_college_id');
        DB::statement('ALTER TABLE college_individual_messages DROP INDEX idx_college_id');
        DB::statement('ALTER TABLE college_individual_messages DROP INDEX idx_college_dept_id');
        DB::statement('ALTER TABLE college_individual_messages DROP INDEX idx_year');
        DB::statement('ALTER TABLE college_messages DROP INDEX idx_college_id');
        DB::statement('ALTER TABLE college_notices DROP INDEX idx_college_id');
        DB::statement('ALTER TABLE college_offline_paper_marks DROP INDEX idx_college_id');
        DB::statement('ALTER TABLE college_offline_paper_marks DROP INDEX idx_college_subject_id');
        DB::statement('ALTER TABLE college_payments DROP INDEX idx_college_id');
        DB::statement('ALTER TABLE college_payments DROP INDEX idx_user_id');
        DB::statement('ALTER TABLE college_subjects DROP INDEX idx_college_id');
        DB::statement('ALTER TABLE college_time_tables DROP INDEX idx_college_id');
        DB::statement('ALTER TABLE college_time_tables DROP INDEX idx_college_dept_id');
        DB::statement('ALTER TABLE college_time_tables DROP INDEX idx_year');
        DB::statement('ALTER TABLE college_user_attendances DROP INDEX idx_college_id');
        DB::statement('ALTER TABLE college_user_attendances DROP INDEX idx_college_dept_id');
        DB::statement('ALTER TABLE college_user_attendances DROP INDEX idx_year');
        DB::statement('ALTER TABLE college_user_attendances DROP INDEX idx_college_subject_id');
        DB::statement('ALTER TABLE discussion_posts DROP INDEX idx_category_id');
        DB::statement('ALTER TABLE discussion_posts DROP INDEX idx_user_id');
        DB::statement('ALTER TABLE exam_patterns DROP INDEX idx_placement_area_id');
        DB::statement('ALTER TABLE exam_patterns DROP INDEX idx_placement_company_id');
        DB::statement('ALTER TABLE mentors DROP INDEX idx_mentor_area_id');
        DB::statement('ALTER TABLE mentor_chat_messages DROP INDEX idx_mentor_chat_room_id');
        DB::statement('ALTER TABLE mentor_chat_messages DROP INDEX idx_sender_id');
        DB::statement('ALTER TABLE mentor_chat_messages DROP INDEX idx_receiver_id');
        DB::statement('ALTER TABLE mentor_ratings DROP INDEX idx_mentor_id');
        DB::statement('ALTER TABLE mentor_ratings DROP INDEX idx_user_id');
        DB::statement('ALTER TABLE mentor_schedules DROP INDEX idx_mentor_id');
        DB::statement('ALTER TABLE mentor_schedules DROP INDEX idx_user_id');
        DB::statement('ALTER TABLE mentor_skills DROP INDEX idx_mentor_area_id');
        DB::statement('ALTER TABLE mock_interview_reviews DROP INDEX idx_user_data_id');
        DB::statement('ALTER TABLE mock_interview_reviews DROP INDEX idx_user_id');
        DB::statement('ALTER TABLE payments DROP INDEX idx_client_plan_id');
        DB::statement('ALTER TABLE placement_companies DROP INDEX idx_placement_area_id');
        DB::statement('ALTER TABLE placement_experiances DROP INDEX idx_placement_area_id');
        DB::statement('ALTER TABLE placement_experiances DROP INDEX idx_placement_company_id');
        DB::statement('ALTER TABLE placement_faqs DROP INDEX idx_placement_area_id');
        DB::statement('ALTER TABLE placement_faqs DROP INDEX idx_placement_company_id');
        DB::statement('ALTER TABLE placement_processes DROP INDEX idx_placement_area_id');
        DB::statement('ALTER TABLE placement_processes DROP INDEX idx_placement_company_id');
        DB::statement('ALTER TABLE placement_process_comments DROP INDEX idx_company_id');
        DB::statement('ALTER TABLE placement_process_comments DROP INDEX idx_user_id');
        DB::statement('ALTER TABLE placement_process_comment_likes DROP INDEX idx_company_id');
        DB::statement('ALTER TABLE placement_process_comment_likes DROP INDEX idx_user_id');
        DB::statement('ALTER TABLE placement_process_comment_likes DROP INDEX idx_placement_process_comment_id');
        DB::statement('ALTER TABLE placement_process_likes DROP INDEX idx_company_id');
        DB::statement('ALTER TABLE placement_process_likes DROP INDEX idx_user_id');
        DB::statement('ALTER TABLE placement_process_sub_comments DROP INDEX idx_company_id');
        DB::statement('ALTER TABLE placement_process_sub_comments DROP INDEX idx_user_id');
        DB::statement('ALTER TABLE placement_process_sub_comments DROP INDEX idx_placement_process_comment_id');
        DB::statement('ALTER TABLE placement_process_sub_comment_likes DROP INDEX idx_company_id');
        DB::statement('ALTER TABLE placement_process_sub_comment_likes DROP INDEX idx_user_id');
        DB::statement('ALTER TABLE placement_process_sub_comment_likes DROP INDEX idx_placement_process_comment_id');
        DB::statement('ALTER TABLE placement_process_sub_comment_likes DROP INDEX idx_placement_process_sub_comment_id');
        DB::statement('ALTER TABLE question_bank_questions DROP INDEX idx_category_id_subcat_id');
        DB::statement('ALTER TABLE question_bank_sub_categories DROP INDEX idx_question_bank_category_id');
        DB::statement('ALTER TABLE ratings DROP INDEX idx_module_id');
        DB::statement('ALTER TABLE ratings DROP INDEX idx_user_id');
        DB::statement('ALTER TABLE ratings DROP INDEX idx_module_type');
        DB::statement('ALTER TABLE read_notifications DROP INDEX idx_notification_id');
        DB::statement('ALTER TABLE read_notifications DROP INDEX idx_notification_module');
        DB::statement('ALTER TABLE read_notifications DROP INDEX idx_user_id');
        DB::statement('ALTER TABLE read_notifications DROP INDEX idx_created_module_id');
        DB::statement('ALTER TABLE register_documents DROP INDEX idx_user_id');
        DB::statement('ALTER TABLE register_documents DROP INDEX idx_documents_docs_id');
        DB::statement('ALTER TABLE register_favourite_documents DROP INDEX idx_user_id');
        DB::statement('ALTER TABLE register_favourite_documents DROP INDEX idx_documents_docs_id');
        DB::statement('ALTER TABLE register_online_courses DROP INDEX idx_user_id');
        DB::statement('ALTER TABLE register_online_courses DROP INDEX idx_online_course_id');
        DB::statement('ALTER TABLE register_papers DROP INDEX idx_user_id');
        DB::statement('ALTER TABLE register_papers DROP INDEX idx_test_subject_paper_id');
        DB::statement('ALTER TABLE register_projects DROP INDEX idx_user_id');
        DB::statement('ALTER TABLE register_projects DROP INDEX idx_project_id');
        DB::statement('ALTER TABLE study_material_comments DROP INDEX idx_study_material_topic_id');
        DB::statement('ALTER TABLE study_material_comments DROP INDEX idx_study_material_post_id');
        DB::statement('ALTER TABLE study_material_comments DROP INDEX idx_user_id');
        DB::statement('ALTER TABLE study_material_comment_likes DROP INDEX idx_study_material_topic_id');
        DB::statement('ALTER TABLE study_material_comment_likes DROP INDEX idx_study_material_post_id');
        DB::statement('ALTER TABLE study_material_comment_likes DROP INDEX idx_study_material_comment_id');
        DB::statement('ALTER TABLE study_material_comment_likes DROP INDEX idx_user_id');

        DB::statement('ALTER TABLE study_material_posts DROP INDEX idx_course_category_id');
        DB::statement('ALTER TABLE study_material_posts DROP INDEX idx_course_sub_category_id');
        DB::statement('ALTER TABLE study_material_posts DROP INDEX idx_study_material_subject_id');
        DB::statement('ALTER TABLE study_material_posts DROP INDEX idx_study_material_topic_id');

        DB::statement('ALTER TABLE study_material_post_likes DROP INDEX idx_study_material_topic_id');
        DB::statement('ALTER TABLE study_material_post_likes DROP INDEX idx_study_material_post_id');
        DB::statement('ALTER TABLE study_material_post_likes DROP INDEX idx_user_id');

        DB::statement('ALTER TABLE study_material_subjects DROP INDEX idx_course_category_id');
        DB::statement('ALTER TABLE study_material_subjects DROP INDEX idx_course_sub_category_id');
        DB::statement('ALTER TABLE study_material_subjects DROP INDEX idx_admin_id');

        DB::statement('ALTER TABLE study_material_sub_comments DROP INDEX idx_study_material_topic_id');
        DB::statement('ALTER TABLE study_material_sub_comments DROP INDEX idx_study_material_post_id');
        DB::statement('ALTER TABLE study_material_sub_comments DROP INDEX idx_study_material_comment_id');
        DB::statement('ALTER TABLE study_material_sub_comments DROP INDEX idx_user_id');

        DB::statement('ALTER TABLE study_material_sub_comment_likes DROP INDEX idx_study_material_topic_id');
        DB::statement('ALTER TABLE study_material_sub_comment_likes DROP INDEX idx_study_material_post_id');
        DB::statement('ALTER TABLE study_material_sub_comment_likes DROP INDEX idx_study_material_comment_id');
        DB::statement('ALTER TABLE study_material_sub_comment_likes DROP INDEX idx_study_material_sub_comment_id');
        DB::statement('ALTER TABLE study_material_sub_comment_likes DROP INDEX idx_user_id');

        DB::statement('ALTER TABLE study_material_topics DROP INDEX idx_course_category_id');
        DB::statement('ALTER TABLE study_material_topics DROP INDEX idx_course_sub_category_id');
        DB::statement('ALTER TABLE study_material_topics DROP INDEX idx_study_material_subject_id');

        DB::statement('ALTER TABLE test_sub_categories DROP INDEX idx_test_category_id');

        DB::statement('ALTER TABLE user_datas DROP INDEX idx_category_id');
        DB::statement('ALTER TABLE user_datas DROP INDEX idx_sub_category_id');
        DB::statement('ALTER TABLE user_datas DROP INDEX idx_subject_id');
        DB::statement('ALTER TABLE user_datas DROP INDEX idx_paper_id');
        DB::statement('ALTER TABLE user_datas DROP INDEX idx_user_id');
        DB::statement('ALTER TABLE user_datas DROP INDEX idx_college_id');

        DB::statement('ALTER TABLE user_solutions DROP INDEX idx_ques_id');
        DB::statement('ALTER TABLE user_solutions DROP INDEX idx_user_id');
        DB::statement('ALTER TABLE user_solutions DROP INDEX idx_subject_id');
        DB::statement('ALTER TABLE user_solutions DROP INDEX idx_paper_id');
        DB::statement('ALTER TABLE user_solutions DROP INDEX idx_score_id');

        DB::statement('ALTER TABLE vkit_projects DROP INDEX idx_category_id');

        DB::statement('ALTER TABLE vkit_project_sub_comments DROP INDEX idx_vkit_project_id');
        DB::statement('ALTER TABLE vkit_project_sub_comments DROP INDEX idx_vkit_project_comment_id');
        DB::statement('ALTER TABLE vkit_project_sub_comments DROP INDEX idx_user_id');

        DB::statement('ALTER TABLE zero_to_heroes DROP INDEX idx_designation_id');
        DB::statement('ALTER TABLE zero_to_heroes DROP INDEX idx_area_id');
    }
}
