<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('assessment_id')->unsigned();
            $table->integer('language_id')->unsigned();
            $table->text('name');
            $table->text('description');
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users');

            $table->foreign('language_id')
                ->references('id')
                ->on('languages')
                ->onDelete('cascade');

            $table->foreign('assessment_id')
                ->references('id')
                ->on('assessments')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('translations');
    }
}
