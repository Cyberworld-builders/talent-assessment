<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBenchmarksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('benchmarks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('dimension_id')->unsigned();
            $table->integer('industry_id')->unsigned();
            $table->text('value');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('dimension_id')
                ->references('id')
                ->on('dimensions')
                ->onDelete('cascade');

            $table->foreign('industry_id')
                ->references('id')
                ->on('industries')
                ->onDelete('cascade');

            // Unique constraint to prevent duplicate benchmarks
            $table->unique(['dimension_id', 'industry_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('benchmarks');
    }
}
