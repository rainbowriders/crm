<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLogsTable extends Migration {

	public function up()
	{
		Schema::create('logs', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('userID')->unsigned();
			$table->string('action', 255);
			$table->string('old', 255);
			$table->string('new', 255);
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('logs');
	}
}