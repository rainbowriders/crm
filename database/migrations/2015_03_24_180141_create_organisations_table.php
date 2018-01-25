<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOrganisationsTable extends Migration {

	public function up()
	{
		Schema::create('organisations', function(Blueprint $table) {
			$table->increments('id');
			$table->string('name', 255);
			$table->integer('accountID')->unsigned();
			$table->string('address', 255)->nullable();
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('organisations');
	}
}