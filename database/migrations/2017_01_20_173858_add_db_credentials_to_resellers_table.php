<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDbCredentialsToResellersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::table('resellers', function (Blueprint $table) {
			$table->text('db_host')->nullable();
			$table->text('db_user')->nullable();
			$table->text('db_instance')->nullable();
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::table('resellers', function (Blueprint $table) {
			$table->dropColumn('db_host');
			$table->dropColumn('db_user');
			$table->dropColumn('db_instance');
		});
    }
}
