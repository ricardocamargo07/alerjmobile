<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocumentsPagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('documents_pages', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('document_id')->nullable();
			$table->string('position');
			$table->integer('level');
			$table->string('alerj_id')->nullable();
			$table->string('title');
			$table->text('page')->nullable();
			$table->timestamps();

			$table->foreign('document_id')
				->references('id')
				->on('documents')
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
		Schema::drop('documents_pages');
	}

}
