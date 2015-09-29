<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserLoginTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        $tableName = \Config::get("kandy-laravel.kandy_user_login_table");
        Schema::create(
            $tableName,
            function (Blueprint $table) {
                $table->increments('id');
                $table->string('kandy_user_id');// kandy user id
                $table->integer('type');
                $table->integer('status')->default(0);// status: 1 - online, 0 - offline
                $table->string('browser_agent');// total rating point
                $table->string('ip_address');
                $table->integer('time');
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
