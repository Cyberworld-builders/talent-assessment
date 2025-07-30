<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmailAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create('email_addresses', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('assessment_id')->unsigned();
			$table->text('name')->nullable();
			$table->text('email')->nullable();
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
		Schema::drop('email_addresses');
    }
}
