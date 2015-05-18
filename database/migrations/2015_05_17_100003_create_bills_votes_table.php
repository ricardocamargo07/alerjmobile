<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBillsVotesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('bills_votes', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('bill_id')->unsigned();
			$table->integer('congressman_id')->unsigned();
			$table->integer('plenary_session_id')->unsigned();
			$table->string('vote');
			$table->timestamps();

			$table->foreign('bill_id')
				->references('id')
				->on('bills')
				->onUpdate('cascade')
				->onDelete('cascade');

			$table->foreign('congressman_id')
				->references('id')
				->on('congressmen')
				->onUpdate('cascade')
				->onDelete('cascade');

			$table->foreign('plenary_session_id')
				->references('id')
				->on('plenary_sessions')
				->onUpdate('cascade')
				->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('bills_votes');
	}

}
