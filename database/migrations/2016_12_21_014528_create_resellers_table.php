<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResellersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('resellers', function (Blueprint $table) {
            $table->increments('id');
			$table->text('name');
			$table->text('db_name');
			$table->text('db_pass')->nullable();
			$table->text('logo')->nullable();
			$table->text('background')->nullable();
			$table->text('primary_color')->nullable();
			$table->text('accent_color')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('resellers');
    }
}
