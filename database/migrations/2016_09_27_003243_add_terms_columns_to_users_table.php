<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTermsColumnsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::table('users', function(Blueprint $table) {
			$table->boolean('accepted_terms')->nullable();
			$table->timestamp('accepted_at')->nullable();
			$table->string('accepted_signature')->nullable();
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::table('users', function(Blueprint $table) {
			$table->dropColumn('accepted_terms');
			$table->dropColumn('accepted_at');
			$table->dropColumn('accepted_signature');
		});
    }
}
