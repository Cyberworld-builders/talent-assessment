<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTargetIdToAsssignmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::table('assignments', function (Blueprint $table) {
			$table->integer('target_id')->unsigned();
//			$table->foreign('target_id')
//				->references('id')
//				->on('users');
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
//			$table->dropForeign('assignments_target_id_foreign');
			$table->dropColumn('target_id');
		});
    }
}
