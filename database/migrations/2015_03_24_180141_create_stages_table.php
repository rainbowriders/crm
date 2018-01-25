<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateStagesTable extends Migration {

	public function up()
	{
		Schema::create('stages', function(Blueprint $table) {
			$table->increments('id');
			$table->string('title', 255);
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('stages');
	}
}