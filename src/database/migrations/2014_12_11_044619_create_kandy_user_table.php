<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateKandyUserTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tableName = \Config::get("kandy-laravel.kandy_user_table");

        Schema::create(
            $tableName,
            function (Blueprint $table) {
                $table->increments('id');
                $table->string('user_id');
                $table->string('first_name')->nullable();
                $table->string('last_name')->nullable();
                $table->string('password');
                $table->string('email')->nullable()->default(null);
                $table->string('domain_name');
                $table->string('api_key');
                $table->string('api_secret');
                $table->string('main_user_id')->nullable()->default(null);
                //add type of user (e.g: support agent,...)
                $table->tinyInteger('type');
                //add presence_status of user (e.g: support chat and presence list)
                $table->tinyInteger('presence_status')->default(0);
                $table->timestamps();
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
