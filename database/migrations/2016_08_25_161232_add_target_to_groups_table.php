<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTargetToGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
	public function up()
	{
		Schema::table('groups', function (Blueprint $table) {
			$table->integer('target_id')->nullable()->unsigned()->index();
			$table->foreign('target_id')->references('id')->on('users');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('groups', function (Blueprint $table) {
			$table->dropForeign('groups_target_id_foreign');
			$table->dropColumn('target_id');
		});
	}
}
