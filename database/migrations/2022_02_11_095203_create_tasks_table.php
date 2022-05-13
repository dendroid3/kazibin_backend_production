<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->uuid('broker_id');
            $table->uuid('writer_id')->nullable();
            $table->integer('status');
            $table->string('topic');
            $table->string('unit');
            $table->integer('pages') ->nullable();
            $table->integer('page_cost') ->nullable();
            $table->integer('full_pay') ->nullable();
            $table->integer('difficulty') ->nullable();
            $table->longText('instructions');
            $table->string('type');
            $table->string('takers')->nullable() ->nullable();
            $table->string('code')->nullable();
            $table->dateTime('expiry_time')->nullable();
            $table->dateTime('pay_day') ->nullable();
            $table->timestamps();

            $table->index('broker_id');
            $table->index('writer_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasks');
    }
}
