<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCustomFieldsToAssessmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('assessments', function(Blueprint $table) {
            $table->boolean('use_custom_fields')->nullable();
            $table->string('custom_fields')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('assessments', function(Blueprint $table) {
            $table->dropColumn('use_custom_fields');
            $table->dropColumn('custom_fields');
        });
    }
}
