<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCongressmenTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('congressmen', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name');
			$table->string('email')->unique();
			$table->integer('party_id')->unsigned()->index();
			$table->integer('alerj_id')->unsigned();
			$table->string('url');
			$table->text('page')->nullable();
			$table->timestamps();

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
		Schema::drop('congressmen');
	}

}
