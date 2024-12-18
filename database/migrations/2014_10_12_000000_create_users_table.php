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
            $table->string('interests')->nullable();
            $table->string('code') -> nullable();
            $table->boolean('availabile') -> nullable();
            $table->string('email')->nullable();
            $table->integer('cost_per_page')->nullable();
            $table->integer('broker_score')->nullable();
            $table->integer('writer_score')->nullable();
            $table->string('pay_day')->nullable();
            $table->integer('phone_number')->nullable();
            $table->string('level')->nullable();
            $table->string('course')->nullable();
            $table->longText('bio')->nullable();
            $table->longText('email_verification')->nullable();
            $table->string('phone_verification')->nullable();
            $table->boolean('credential_verification')->nullable();
            $table->longText('password');
            $table->string('role')->default('User');
            $table->dateTime('last_activity');
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
