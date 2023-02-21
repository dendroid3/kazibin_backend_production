<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaskoffersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('taskoffers', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->integer('status')->default(1);
            $table->uuid('broker_id');
            $table->uuid('task_id');
            $table->uuid('writer_id');
            $table->timestamps();

            $table -> index('task_id');
            $table -> index('writer_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('taskoffers');
    }
}
