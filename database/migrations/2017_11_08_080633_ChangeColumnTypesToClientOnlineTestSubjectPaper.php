<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeColumnTypesToClientOnlineTestSubjectPaper extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection('mysql2')->statement('ALTER TABLE client_online_test_subject_papers MODIFY COLUMN date_to_active DATETIME');
        DB::connection('mysql2')->statement('ALTER TABLE client_online_test_subject_papers MODIFY COLUMN date_to_inactive DATETIME');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::connection('mysql2')->statement('ALTER TABLE client_online_test_subject_papers MODIFY COLUMN date_to_active DATE');
        DB::connection('mysql2')->statement('ALTER TABLE client_online_test_subject_papers MODIFY COLUMN date_to_inactive DATE');
    }
}
