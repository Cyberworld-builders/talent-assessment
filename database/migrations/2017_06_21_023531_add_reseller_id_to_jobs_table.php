<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddResellerIdToJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
	public function up()
	{
		Schema::table('jobs', function (Blueprint $table) {
			$table->integer('reseller_id')->nullable()->unsigned()->index();
			$table->foreign('reseller_id')->references('id')->on('resellers');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('jobs', function (Blueprint $table) {
			$table->dropForeign('jobs_reseller_id_foreign');
			$table->dropColumn('reseller_id');
		});
	}
}
