<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddReportUrlsToReportDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('report_data', function (Blueprint $table) {
            $table->string('slug')->nullable()->index()->after('assignment_id');
            $table->string('html_url')->nullable()->after('slug');
            $table->string('pdf_url')->nullable()->after('html_url');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('report_data', function (Blueprint $table) {
            $table->dropColumn(['slug', 'html_url', 'pdf_url']);
        });
    }
}
