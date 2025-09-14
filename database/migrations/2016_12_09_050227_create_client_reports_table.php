<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_reports', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('report_id')->unsigned();
            $table->integer('client_id')->unsigned();
			$table->text('fields')->nullable();
            $table->boolean('enabled')->default(0);
            $table->boolean('visible')->default(0);
            $table->timestamp('updated_at');
            $table->timestamp('created_at');

            $table->foreign('client_id')
				->references('id')
                ->on('clients')
                ->onDelete('cascade');

            $table->foreign('report_id')
                ->references('id')
                ->on('reports')
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
        Schema::drop('client_reports');
    }
}
