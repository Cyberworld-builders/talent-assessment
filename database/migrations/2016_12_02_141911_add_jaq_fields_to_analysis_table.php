<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddJaqFieldsToAnalysisTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('analysis', function(Blueprint $table) {
			$table->text('name');
			$table->text('job_code')->nullable();
			$table->text('department_name')->nullable();
			$table->text('location')->nullable();
			$table->text('position')->nullable();
			$table->text('supervisor_title')->nullable();
			$table->text('tasks')->nullable();
			$table->text('ksas')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('analysis', function(Blueprint $table) {
			$table->dropColumn('name');
			$table->dropColumn('job_code');
			$table->dropColumn('department_name');
			$table->dropColumn('location');
			$table->dropColumn('position');
			$table->dropColumn('supervisor_title');
			$table->dropColumn('tasks');
			$table->dropColumn('ksas');
		});
	}
}
