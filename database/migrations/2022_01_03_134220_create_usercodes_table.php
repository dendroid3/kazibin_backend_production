<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsercodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usercodes', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->uuid('user_id')->unique();
            $table->integer('writer_score');
            $table->integer('broker_score');
            $table->integer('net_score');
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
        Schema::dropIfExists('usercodes');
    }
}
