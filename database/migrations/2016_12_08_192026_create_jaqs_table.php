<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJaqsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jaqs', function (Blueprint $table) {
            $table->increments('id');
			$table->integer('analysis_id')->unsigned();
			$table->integer('user_id')->unsigned();
			$table->text('position')->nullable();
			$table->text('job_code')->nullable();
			$table->text('department_name')->nullable();
			$table->text('location')->nullable();
			$table->text('supervisor_name')->nullable();
			$table->text('supervisor_title')->nullable();
			$table->text('position_desc')->nullable();
			$table->text('tasks')->nullable();
			$table->text('ksas')->nullable();
			$table->text('ksa_linkages')->nullable();
			$table->text('min_education')->nullable();
			$table->text('preferred_education')->nullable();
			$table->text('min_experience')->nullable();
			$table->text('preferred_experience')->nullable();
			$table->text('additional_requirements')->nullable();
			$table->boolean('sent')->default(0);
			$table->boolean('completed')->default(0);
			$table->timestamp('sent_at');
			$table->timestamp('completed_at');
            $table->timestamps();

			$table->foreign('user_id')
				->references('id')
				->on('users')
				->onDelete('cascade');

			$table->foreign('analysis_id')
				->references('id')
				->on('analysis')
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
        Schema::drop('jaqs');
    }
}
