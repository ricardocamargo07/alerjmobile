<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRegimentTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('regiment', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('position');
			$table->integer('level');
			$table->string('document_id')->nullable();
			$table->string('title');
			$table->text('page')->nullable();
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
		Schema::drop('regiment');
	}

}
