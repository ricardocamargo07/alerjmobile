<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameHasContentsColumn extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('documents_pages', function(Blueprint $table)
		{
			$table->renameColumn('has_contents', 'has_content')->default(true);
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
            $table->renameColumn('has_contents', 'has_content');
        });
	}

}
