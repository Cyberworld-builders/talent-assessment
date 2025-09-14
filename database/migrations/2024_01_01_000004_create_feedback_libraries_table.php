<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFeedbackLibrariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('feedback_libraries', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('client_id')->unsigned()->nullable();
            $table->string('name')->unique();
            $table->json('feedback');
            $table->timestamps();

            $table->foreign('client_id')
                ->references('id')
                ->on('clients')
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
        Schema::table('feedback_libraries', function (Blueprint $table) {
            $table->dropForeign('feedback_libraries_client_id_foreign');
        });
        Schema::drop('feedback_libraries');
    }
}
