<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaskoffermessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('taskoffermessages', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->uuid('user_id');
            $table->uuid('taskoffer_id');
            $table->longText('message');
            $table->string('type')->default('text');
            $table->dateTime('delivered_at') -> nullable();
            $table->dateTime('read_at') -> nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('taskoffer_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('taskoffermessages');
    }
}
