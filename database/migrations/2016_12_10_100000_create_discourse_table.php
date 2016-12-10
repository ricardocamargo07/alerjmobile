<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDiscourseTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('discourses', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('alerj_id')->index();
            $table->string('title');
            $table->integer('congressman_id')->unsigned();
            $table->string('session_type')->nullable(); // sessao ordinaria
            $table->string('expedient_type')->nullable(); // expediente final
            $table->string('document_type')->nullable(); // discurso
            $table->timestamp('datetime');
            $table->text('document');
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
		Schema::drop('discourses');
	}
}
