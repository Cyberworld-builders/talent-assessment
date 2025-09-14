<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddJobIdToAssignmentsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('assignments', function (Blueprint $table) {
			$table->integer('job_id')->nullable()->unsigned()->index();
			$table->foreign('job_id')->references('id')->on('jobs');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('assignments', function (Blueprint $table) {
			$table->dropForeign('assignments_job_id_foreign');
			$table->dropColumn('job_id');
		});
	}
}
