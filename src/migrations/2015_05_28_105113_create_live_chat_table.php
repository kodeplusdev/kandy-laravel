<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLiveChatTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$tableName = \Config::get("kandy-laravel::kandy_live_chat_table");
        Schema::create(
            $tableName,
            function (Blueprint $table) {
                $table->increments('id');
                $table->string('agent_user_id');// kandy user id of agent
                $table->string('customer_user_id');//kandy user id of customer
                $table->string('customer_name');
                $table->string('customer_email');
                $table->integer('last_time')->default(0)->unsigned();// the last time connect
                $table->integer('first_time')->nullable()->unsigned();// the first time connect
                $table->tinyInteger('times')->default(0)->unsigned();// this pair(agent, customer) connect how many times?
                $table->bigInteger('last_chat')->default(0)->unsigned();//the last interaction time
                //$table->primary(array('agent_user_id', 'customer_email'));
                $table->index(array('customer_email', 'agent_user_id'));
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
