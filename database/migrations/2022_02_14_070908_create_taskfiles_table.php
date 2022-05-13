<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaskfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('taskfiles', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->uuid('task_id');
            $table->longText('url');
            $table->string('name');
            $table->timestamps();

            $table->index('task_id');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('taskfiles');
    }
}
