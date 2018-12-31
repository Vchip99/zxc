<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexesToClientDb extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection('mysql2')->statement('ALTER TABLE `bank_details` ADD INDEX idx_client_id (`client_id`)');
        DB::connection('mysql2')->statement('ALTER TABLE `clientusers` ADD INDEX idx_client_id (`client_id`)');
        DB::connection('mysql2')->statement('ALTER TABLE `client_assignment_subjects` ADD INDEX idx_client_id (`client_id`)');
        DB::connection('mysql2')->statement('ALTER TABLE `client_batches` ADD INDEX idx_client_id (`client_id`)');
        DB::connection('mysql2')->statement('ALTER TABLE `client_chat_messages` ADD INDEX idx_client_chat_room_id (`client_chat_room_id`)');
        DB::connection('mysql2')->statement('ALTER TABLE `client_chat_messages` ADD INDEX idx_sender_id (`sender_id`)');
        DB::connection('mysql2')->statement('ALTER TABLE `client_chat_messages` ADD INDEX idx_receiver_id (`receiver_id`)');
        DB::connection('mysql2')->statement('ALTER TABLE `client_chat_messages` ADD INDEX idx_client_id (`client_id`)');
        DB::connection('mysql2')->statement('ALTER TABLE `client_classes` ADD INDEX idx_client_id (`client_id`)');
        DB::connection('mysql2')->statement('ALTER TABLE `client_discussion_categories` ADD INDEX idx_client_id (`client_id`)');
        DB::connection('mysql2')->statement('ALTER TABLE `client_discussion_comments` ADD INDEX idx_client_discussion_post_id (`client_discussion_post_id`)');
        DB::connection('mysql2')->statement('ALTER TABLE `client_discussion_comments` ADD INDEX idx_client_id (`client_id`)');
        DB::connection('mysql2')->statement('ALTER TABLE `client_discussion_likes` ADD INDEX idx_client_discussion_post_id (`client_discussion_post_id`)');
        DB::connection('mysql2')->statement('ALTER TABLE `client_discussion_likes` ADD INDEX idx_client_id (`client_id`)');
        DB::connection('mysql2')->statement('ALTER TABLE `client_discussion_posts` ADD INDEX idx_client_discussion_category_id (`client_discussion_category_id`)');
        DB::connection('mysql2')->statement('ALTER TABLE `client_discussion_posts` ADD INDEX idx_client_id (`client_id`)');
        DB::connection('mysql2')->statement('ALTER TABLE `client_discussion_sub_comments` ADD INDEX idx_client_discussion_post_id (`client_discussion_post_id`)');
        DB::connection('mysql2')->statement('ALTER TABLE `client_discussion_sub_comments` ADD INDEX idx_client_discussion_comment_id (`client_discussion_comment_id`)');
        DB::connection('mysql2')->statement('ALTER TABLE `client_discussion_sub_comments` ADD INDEX idx_client_id (`client_id`)');
        DB::connection('mysql2')->statement('ALTER TABLE `client_exams` ADD INDEX idx_client_id (`client_id`)');
        DB::connection('mysql2')->statement('ALTER TABLE `client_gallery_images` ADD INDEX idx_client_gallery_type_id (`client_gallery_type_id`)');
        DB::connection('mysql2')->statement('ALTER TABLE `client_gallery_images` ADD INDEX idx_client_id (`client_id`)');
        DB::connection('mysql2')->statement('ALTER TABLE `client_gallery_types` ADD INDEX idx_client_id (`client_id`)');
        DB::connection('mysql2')->statement('ALTER TABLE `client_holidays` ADD INDEX idx_client_id (`client_id`)');
        DB::connection('mysql2')->statement('ALTER TABLE `client_home_pages` ADD INDEX idx_client_id (`client_id`)');
        DB::connection('mysql2')->statement('ALTER TABLE `client_individual_messages` ADD INDEX idx_client_batch_id (`client_batch_id`)');
        DB::connection('mysql2')->statement('ALTER TABLE `client_individual_messages` ADD INDEX idx_client_id (`client_id`)');
        DB::connection('mysql2')->statement('ALTER TABLE `client_login_activities` ADD INDEX idx_client_id (`client_id`)');
        DB::connection('mysql2')->statement('ALTER TABLE `client_messages` ADD INDEX idx_client_id (`client_id`)');
        DB::connection('mysql2')->statement('ALTER TABLE `client_notices` ADD INDEX idx_client_id (`client_id`)');
        DB::connection('mysql2')->statement('ALTER TABLE `client_offline_paper_marks` ADD INDEX idx_client_batch_id (`client_batch_id`)');
        DB::connection('mysql2')->statement('ALTER TABLE `client_offline_paper_marks` ADD INDEX idx_client_exam_id (`client_exam_id`)');
        DB::connection('mysql2')->statement('ALTER TABLE `client_offline_paper_marks` ADD INDEX idx_client_id (`client_id`)');
        DB::connection('mysql2')->statement('ALTER TABLE `client_offline_payments` ADD INDEX idx_client_batch_id (`client_batch_id`)');
        DB::connection('mysql2')->statement('ALTER TABLE `client_offline_payments` ADD INDEX idx_clientuser_id (`clientuser_id`)');
        DB::connection('mysql2')->statement('ALTER TABLE `client_offline_payments` ADD INDEX idx_client_id (`client_id`)');
        DB::connection('mysql2')->statement('ALTER TABLE `client_receipts` ADD INDEX idx_client_id (`client_id`)');
        DB::connection('mysql2')->statement('ALTER TABLE `client_teams` ADD INDEX idx_client_id (`client_id`)');
        DB::connection('mysql2')->statement('ALTER TABLE `client_testimonials` ADD INDEX idx_client_id (`client_id`)');
        DB::connection('mysql2')->statement('ALTER TABLE `client_upload_transactions` ADD INDEX idx_client_batch_id (`client_batch_id`)');
        DB::connection('mysql2')->statement('ALTER TABLE `client_upload_transactions` ADD INDEX idx_clientuser_id (`clientuser_id`)');
        DB::connection('mysql2')->statement('ALTER TABLE `client_upload_transactions` ADD INDEX idx_client_id (`client_id`)');
        DB::connection('mysql2')->statement('ALTER TABLE `client_user_attendances` ADD INDEX idx_client_batch_id (`client_batch_id`)');
        DB::connection('mysql2')->statement('ALTER TABLE `client_user_attendances` ADD INDEX idx_client_id (`client_id`)');
        DB::connection('mysql2')->statement('ALTER TABLE `payable_client_sub_categories` ADD INDEX idx_client_id (`client_id`)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::connection('mysql2')->statement('ALTER TABLE bank_details DROP INDEX idx_client_id');
        DB::connection('mysql2')->statement('ALTER TABLE clientusers DROP INDEX idx_client_id');
        DB::connection('mysql2')->statement('ALTER TABLE client_assignment_subjects DROP INDEX idx_client_id');
        DB::connection('mysql2')->statement('ALTER TABLE client_batches DROP INDEX idx_client_id');
        DB::connection('mysql2')->statement('ALTER TABLE client_chat_messages DROP INDEX idx_client_chat_room_id');
        DB::connection('mysql2')->statement('ALTER TABLE client_chat_messages DROP INDEX idx_sender_id');
        DB::connection('mysql2')->statement('ALTER TABLE client_chat_messages DROP INDEX idx_receiver_id');
        DB::connection('mysql2')->statement('ALTER TABLE client_chat_messages DROP INDEX idx_client_id');
        DB::connection('mysql2')->statement('ALTER TABLE client_classes DROP INDEX idx_client_id');
        DB::connection('mysql2')->statement('ALTER TABLE client_discussion_categories DROP INDEX idx_client_id');
        DB::connection('mysql2')->statement('ALTER TABLE client_discussion_comments DROP INDEX idx_client_discussion_post_id');
        DB::connection('mysql2')->statement('ALTER TABLE client_discussion_comments DROP INDEX idx_client_id');
        DB::connection('mysql2')->statement('ALTER TABLE client_discussion_likes DROP INDEX idx_client_discussion_post_id');
        DB::connection('mysql2')->statement('ALTER TABLE client_discussion_likes DROP INDEX idx_client_id');
        DB::connection('mysql2')->statement('ALTER TABLE client_discussion_posts DROP INDEX idx_client_discussion_category_id');
        DB::connection('mysql2')->statement('ALTER TABLE client_discussion_posts DROP INDEX idx_client_id');
        DB::connection('mysql2')->statement('ALTER TABLE client_discussion_sub_comments DROP INDEX idx_client_discussion_post_id');
        DB::connection('mysql2')->statement('ALTER TABLE client_discussion_sub_comments DROP INDEX idx_client_discussion_comment_id');
        DB::connection('mysql2')->statement('ALTER TABLE client_discussion_sub_comments DROP INDEX idx_client_id');
        DB::connection('mysql2')->statement('ALTER TABLE client_exams DROP INDEX idx_client_id');
        DB::connection('mysql2')->statement('ALTER TABLE client_gallery_images DROP INDEX idx_client_gallery_type_id');
        DB::connection('mysql2')->statement('ALTER TABLE client_gallery_images DROP INDEX idx_client_id');
        DB::connection('mysql2')->statement('ALTER TABLE client_gallery_types DROP INDEX idx_client_id');
        DB::connection('mysql2')->statement('ALTER TABLE client_holidays DROP INDEX idx_client_id');
        DB::connection('mysql2')->statement('ALTER TABLE client_home_pages DROP INDEX idx_client_id');
        DB::connection('mysql2')->statement('ALTER TABLE client_individual_messages DROP INDEX idx_client_batch_id');
        DB::connection('mysql2')->statement('ALTER TABLE client_individual_messages DROP INDEX idx_client_id');
        DB::connection('mysql2')->statement('ALTER TABLE client_login_activities DROP INDEX idx_client_id');
        DB::connection('mysql2')->statement('ALTER TABLE client_messages DROP INDEX idx_client_id');
        DB::connection('mysql2')->statement('ALTER TABLE client_notices DROP INDEX idx_client_id');
        DB::connection('mysql2')->statement('ALTER TABLE client_offline_paper_marks DROP INDEX idx_client_batch_id');
        DB::connection('mysql2')->statement('ALTER TABLE client_offline_paper_marks DROP INDEX idx_client_exam_id');
        DB::connection('mysql2')->statement('ALTER TABLE client_offline_paper_marks DROP INDEX idx_client_id');
        DB::connection('mysql2')->statement('ALTER TABLE client_offline_payments DROP INDEX idx_client_batch_id');
        DB::connection('mysql2')->statement('ALTER TABLE client_offline_payments DROP INDEX idx_clientuser_id');
        DB::connection('mysql2')->statement('ALTER TABLE client_offline_payments DROP INDEX idx_client_id');
        DB::connection('mysql2')->statement('ALTER TABLE client_receipts DROP INDEX idx_client_id');
        DB::connection('mysql2')->statement('ALTER TABLE client_teams DROP INDEX idx_client_id');
        DB::connection('mysql2')->statement('ALTER TABLE client_testimonials DROP INDEX idx_client_id');
        DB::connection('mysql2')->statement('ALTER TABLE client_upload_transactions DROP INDEX idx_client_batch_id');
        DB::connection('mysql2')->statement('ALTER TABLE client_upload_transactions DROP INDEX idx_clientuser_id');
        DB::connection('mysql2')->statement('ALTER TABLE client_upload_transactions DROP INDEX idx_client_id');
        DB::connection('mysql2')->statement('ALTER TABLE client_user_attendances DROP INDEX idx_client_batch_id');
        DB::connection('mysql2')->statement('ALTER TABLE client_user_attendances DROP INDEX idx_client_id');
        DB::connection('mysql2')->statement('ALTER TABLE payable_client_sub_categories DROP INDEX idx_client_id');
    }
}
