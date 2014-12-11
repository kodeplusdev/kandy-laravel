<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tableName = \Config::get("kandylaravel::kandy_user_table");
        $userIdColumn = \Config::get("kandylaravel::user_id_column");
        $passwordColumn = \Config::get("kandylaravel::password_column");

        Schema::create(
            $tableName,
            function (Blueprint $table) use ($userIdColumn, $passwordColumn) {
                $table->increments('id');
                $table->string($userIdColumn);
                $table->string($passwordColumn);
                $table->string('main_user_id')->nullable()->default(NULL);
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
