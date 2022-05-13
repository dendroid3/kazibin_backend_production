<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->string('username');
            $table->string('code') -> nullable();
            $table->string('email')->nullable();
            $table->integer('phone_number')->nullable();
            $table->string('level')->nullable();
            $table->string('course')->nullable();
            $table->string('bio')->nullable();
            $table->longText('email_verification')->nullable();
            $table->string('phone_verification')->nullable();
            $table->longText('password');
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
