<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixClientReportsJobIdForeignKey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('client_reports', function (Blueprint $table) {
            // Drop the existing foreign key constraint
            $table->dropForeign('client_reports_job_id_foreign');
            
            // Recreate it to allow NULL values
            $table->foreign('job_id')->references('id')->on('jobs')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('client_reports', function (Blueprint $table) {
            // Drop the nullable foreign key
            $table->dropForeign(['job_id']);
            
            // Recreate the original non-nullable foreign key
            $table->foreign('job_id')->references('id')->on('jobs');
        });
    }
}