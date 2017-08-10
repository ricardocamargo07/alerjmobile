<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImportedTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('import_tables', function(Blueprint $table)
		{
			$table->increments('id');

			$table->string('name')->index();

			$table->timestamps();
		});

        Schema::create('import_records', function(Blueprint $table)
        {
            $table->increments('id');

            $table->integer('import_table_id')->unsigned();

            $table->timestamps();
        });

        Schema::create('import_data', function(Blueprint $table)
        {
            $table->increments('id');

            $table->integer('import_record_id')->unsigned();

            $table->string('field_name');

            $table->string('value');

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
		Schema::drop('import_tables');
	}
}
