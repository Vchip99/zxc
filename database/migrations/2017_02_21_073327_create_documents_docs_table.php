<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocumentsDocsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documents_docs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('author');
            $table->string('introduction');
            $table->integer('doc_category_id')->unsigned();
            $table->boolean('is_paid');
            $table->float('price', 8, 2);
            $table->integer('difficulty_level')->unsigned();
            $table->integer('type_of_document')->unsigned();
            $table->date('date_of_update');
            $table->string('doc_image_path');
            $table->string('doc_pdf_path');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('documents_docs');
    }
}
