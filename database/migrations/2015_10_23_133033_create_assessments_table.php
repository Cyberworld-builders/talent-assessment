<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssessmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assessments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->text('name');
            $table->text('description');
            $table->text('logo');
            $table->text('background');
            $table->boolean('paginate')->nullable();
            $table->integer('items_per_page')->nullable();
            $table->boolean('translation')->nullable();
            $table->text('language')->nullable();
            $table->boolean('whitelabel')->nullable();
            $table->text('company_labeled_for')->nullable();
            $table->text('timed')->nullable();
            $table->integer('time_limit')->nullable();
            $table->timestamps();
            $table->timestamp('last_modified');

            $table->foreign('user_id')
                ->references('id')
                ->on('users');
                //->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('questions'); // drop dependencies first
        Schema::drop('assessments');
    }
}
