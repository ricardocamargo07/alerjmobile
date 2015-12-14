<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddHasContentsColumn extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('documents_pages', function(Blueprint $table)
		{
			$table->boolean('has_contents')->default(true);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('documents_pages', function(Blueprint $table)
        {
            $table->dropColumn('has_contents');
        });
	}

}
