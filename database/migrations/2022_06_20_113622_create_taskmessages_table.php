<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaskmessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('taskmessages', function (Blueprint $table) {
            $table->uuid('id') -> primary() -> unique();
            $table->uuid('user_id');
            $table->uuid('task_id');
            $table->longText('message');
            $table->string('type')->default('text');
            $table->dateTime('delivered_at') -> nullable();
            $table->dateTime('read_at') -> nullable();
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
        Schema::dropIfExists('taskmessages');
    }
}
