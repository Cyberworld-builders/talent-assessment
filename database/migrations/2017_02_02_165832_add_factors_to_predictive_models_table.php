<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFactorsToPredictiveModelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::table('predictive_models', function (Blueprint $table) {
			$table->text('filename')->nullable();
			$table->text('factors')->nullable();
			$table->boolean('configured')->default(0);
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::table('predictive_models', function (Blueprint $table) {
			$table->dropColumn('filename');
			$table->dropColumn('factors');
			$table->dropColumn('configured');
		});
    }
}
