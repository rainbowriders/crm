<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDealsTable extends Migration {

	public function up()
	{
		Schema::create('deals', function(Blueprint $table) {
			$table->increments('id');
			$table->string('title', 255);
			$table->integer('organisationID')->unsigned();
			$table->integer('accountID')->unsigned();
			$table->integer('userID')->unsigned();
			$table->integer('contactID')->unsigned();
			$table->integer('stageID')->unsigned()->nullable();
			$table->integer('value')->default('0');
			$table->tinyInteger('status')->default('0');
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('deals');
	}
}