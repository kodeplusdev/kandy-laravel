<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
        $tableName = \Config::get("kandy-laravel::kandy_user_table");
        Schema::table($tableName, function($table)
        {
            //add type of user (e.g: support agent,...)
            $table->tinyInteger('type');
        });

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		//
	}

}
