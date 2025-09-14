<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAssessmentIdToDimensionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dimensions', function(Blueprint $table) {
            $table->integer('assessment_id')->unsigned()->nullable();

            $table->foreign('assessment_id')
                ->references('id')
                ->on('assessments')
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
        Schema::table('dimensions', function(Blueprint $table) {
            $table->dropForeign('dimensions_assessment_id_foreign');
            $table->dropColumn('assessment_id');
        });
    }
}
