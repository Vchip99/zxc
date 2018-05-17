<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeColumnTypeToTestSubjectPaperTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE test_subject_papers MODIFY COLUMN date_to_active DATETIME');
        DB::statement('ALTER TABLE test_subject_papers MODIFY COLUMN date_to_inactive DATETIME');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE test_subject_papers MODIFY COLUMN date_to_active DATE');
        DB::statement('ALTER TABLE test_subject_papers MODIFY COLUMN date_to_inactive DATE');
    }
}
