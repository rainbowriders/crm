<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateContactsTable extends Migration {

	public function up()
	{
		Schema::create('contacts', function(Blueprint $table) {
			$table->increments('id');
			$table->string('email', 255);
			$table->integer('organisationID')->unsigned();
			$table->string('first_name', 255);
			$table->string('last_name', 255);
			$table->string('phone', 255)->nullable();
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('contacts');
	}
}