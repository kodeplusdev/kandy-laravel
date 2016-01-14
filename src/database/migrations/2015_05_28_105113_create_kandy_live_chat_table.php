<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKandyLiveChatTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$tableName = \Config::get("kandy-laravel.kandy_live_chat_table");
        Schema::create(
            $tableName,
            function (Blueprint $table) {
                $table->increments('id');
                $table->string('agent_user_id');// kandy user id of agent
                $table->string('customer_user_id');//kandy user id of customer
                $table->string('customer_name');
                $table->string('customer_email');
                $table->integer('begin_at')->default(0)->unsigned();// begin chat time
                $table->integer('end_at')->default(0)->unsigned();// end chat time
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
