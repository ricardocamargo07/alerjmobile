<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBillProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bill_projects', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code')->index();
            $table->date('date');
            $table->string('authors', 2048);
            $table->string('year');
            $table->string('month');
            $table->string('number');
            $table->text('description');
            $table->string('url');
            $table->string('site_url');
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
        Schema::drop('bill_projects');
    }
}
