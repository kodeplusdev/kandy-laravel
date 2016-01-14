<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKandyAgentRateTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
        $tableName = \Config::get("kandy-laravel.kandy_live_chat_rate_table");
        Schema::create(
            $tableName,
            function (Blueprint $table) {
                $table->increments('id');
                $table->string('main_user_id');// main user id
                $table->string('rated_by');
                $table->integer('rated_time')->default(0);// rating time
                $table->integer('point');// total rating point
                $table->text('comment');
                $table->index('main_user_id');
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
