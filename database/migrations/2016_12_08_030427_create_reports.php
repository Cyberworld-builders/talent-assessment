<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReports extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		if (! Schema::hasTable('reports'))
		{
			Schema::create('reports', function (Blueprint $table) {
				$table->increments('id');
				$table->text('assessments')->nullable();
				$table->text('view')->nullable();
				$table->text('fields')->nullable();
			});
		}
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::dropIfExists('reports');
    }
}
