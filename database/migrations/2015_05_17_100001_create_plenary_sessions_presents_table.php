<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlenarySessionsPresentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('plenary_sessions_presents', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('plenary_session_id')->unsigned();
			$table->integer('congressman_id')->unsigned();
			$table->integer('party_id')->unsigned();
			$table->timestamps();

			$table->foreign('plenary_session_id')
				->references('id')
				->on('plenary_sessions')
				->onUpdate('cascade')
				->onDelete('cascade');

			$table->foreign('congressman_id')
				->references('id')
				->on('congressmen')
				->onUpdate('cascade')
				->onDelete('cascade');

			$table->foreign('party_id')
				->references('id')
				->on('parties')
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
		Schema::drop('plenary_sessions_presents');
	}

}
