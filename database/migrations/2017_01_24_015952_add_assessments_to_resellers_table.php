<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAssessmentsToResellersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::table('resellers', function(Blueprint $table) {
			$table->string('assessments')->nullable();
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::table('resellers', function(Blueprint $table) {
			$table->dropColumn('assessments');
		});
    }
}
