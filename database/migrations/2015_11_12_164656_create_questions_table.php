<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->increments('id');
            $table->text('content');
            $table->integer('assessment_id')->unsigned();
            $table->integer('number')->unsigned();
            $table->integer('type')->unsigned();
            $table->integer('dimension_id')->unsigned();
            $table->text('anchors');
            $table->timestamps();

            $table->foreign('assessment_id')
                ->references('id')
                ->on('assessments')
                ->onDelete('cascade');

//            $table->foreign('dimension_id')
//                ->references('id')
//                ->on('dimensions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('questions');
    }
}
