<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasktimestampsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasktimestamps', function (Blueprint $table) {
            $table->id();
            $table->uuid('task_id') -> index();
            $table->dateTime('assigned_at') ->nullable();
            $table->dateTime('completed_at') ->nullable();
            $table->dateTime('cancelled_at') ->nullable();
            $table->dateTime('invoiced_at') ->nullable();
            $table->dateTime('pay_initialised_at') ->nullable();
            $table->dateTime('pay_confirmed_at') ->nullable();
            $table->dateTime('cancelation_initiated_at') ->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasktimestamps');
    }
}
