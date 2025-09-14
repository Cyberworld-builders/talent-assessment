<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssignmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assignments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('assessment_id')->unsigned();
            $table->string('url');
            $table->boolean('completed');
            $table->timestamps();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at');
            $table->timestamp('expires');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
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
        Schema::dropIfExists('answers'); // drop dependencies first
        Schema::drop('assignments');
    }
}
