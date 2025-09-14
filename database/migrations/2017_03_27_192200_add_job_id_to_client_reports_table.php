<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddJobIdToClientReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::table('client_reports', function (Blueprint $table) {
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
		Schema::table('client_reports', function (Blueprint $table) {
			$table->dropForeign('client_reports_job_id_foreign');
			$table->dropColumn('job_id');
		});
    }
}
