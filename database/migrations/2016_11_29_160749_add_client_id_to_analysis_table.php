<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddClientIdToAnalysisTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('analysis', function(Blueprint $table) {
			$table->integer('client_id')->unsigned();
			$table->text('users');
			$table->timestamp('sent_at');

			$table->foreign('client_id')
				->references('id')
				->on('clients')
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
		Schema::table('analysis', function(Blueprint $table) {
			$table->dropForeign('analysis_client_id_foreign');
			$table->dropColumn('client_id');
			$table->dropColumn('users');
			$table->dropColumn('sent_at');
		});
	}
}
