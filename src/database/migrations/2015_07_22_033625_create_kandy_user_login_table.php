<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKandyUserLoginTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
        $tableName = 'kandy_user_login';
        Schema::create(
            $tableName,
            function (Blueprint $table) {
                $table->increments('id');
                $table->string('kandy_user_id');// main user id
                $table->tinyInteger('type');
                $table->tinyInteger('status');
                $table->string('browser_agent');// browser agent of user
                $table->text('ip_address');// remote ip address
                $table->integer('time');
                $table->index('kandy_user_id');
            }
        );
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
