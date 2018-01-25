<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Eloquent\Model;

class CreateForeignKeys extends Migration {

	public function up()
	{
		Schema::table('users', function(Blueprint $table) {
			$table->foreign('accountID')->references('id')->on('accounts')
						->onDelete('no action')
						->onUpdate('no action');
		});
		Schema::table('organisations', function(Blueprint $table) {
			$table->foreign('accountID')->references('id')->on('accounts')
						->onDelete('no action')
						->onUpdate('no action');
		});
		Schema::table('contacts', function(Blueprint $table) {
			$table->foreign('organisationID')->references('id')->on('organisations')
						->onDelete('no action')
						->onUpdate('no action');
		});
		Schema::table('deals', function(Blueprint $table) {
			$table->foreign('organisationID')->references('id')->on('organisations')
						->onDelete('no action')
						->onUpdate('no action');
		});
		Schema::table('deals', function(Blueprint $table) {
			$table->foreign('accountID')->references('id')->on('accounts')
						->onDelete('no action')
						->onUpdate('no action');
		});
		Schema::table('deals', function(Blueprint $table) {
			$table->foreign('userID')->references('id')->on('users')
						->onDelete('no action')
						->onUpdate('no action');
		});
		Schema::table('deals', function(Blueprint $table) {
			$table->foreign('contactID')->references('id')->on('contacts')
						->onDelete('no action')
						->onUpdate('no action');
		});
		Schema::table('deals', function(Blueprint $table) {
			$table->foreign('stageID')->references('id')->on('stages')
						->onDelete('no action')
						->onUpdate('no action');
		});
		Schema::table('comments', function(Blueprint $table) {
			$table->foreign('userID')->references('id')->on('users')
						->onDelete('no action')
						->onUpdate('no action');
		});
		Schema::table('logs', function(Blueprint $table) {
			$table->foreign('userID')->references('id')->on('users')
						->onDelete('no action')
						->onUpdate('no action');
		});
	}

	public function down()
	{
		Schema::table('users', function(Blueprint $table) {
			$table->dropForeign('users_accountID_foreign');
		});
		Schema::table('organisations', function(Blueprint $table) {
			$table->dropForeign('organisations_accountID_foreign');
		});
		Schema::table('contacts', function(Blueprint $table) {
			$table->dropForeign('contacts_organisationID_foreign');
		});
		Schema::table('deals', function(Blueprint $table) {
			$table->dropForeign('deals_organisationID_foreign');
		});
		Schema::table('deals', function(Blueprint $table) {
			$table->dropForeign('deals_accountID_foreign');
		});
		Schema::table('deals', function(Blueprint $table) {
			$table->dropForeign('deals_userID_foreign');
		});
		Schema::table('deals', function(Blueprint $table) {
			$table->dropForeign('deals_contactID_foreign');
		});
		Schema::table('deals', function(Blueprint $table) {
			$table->dropForeign('deals_stageID_foreign');
		});
		Schema::table('comments', function(Blueprint $table) {
			$table->dropForeign('comments_userID_foreign');
		});
		Schema::table('logs', function(Blueprint $table) {
			$table->dropForeign('logs_userID_foreign');
		});
	}
}