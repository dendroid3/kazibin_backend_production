<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestmessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('requestmessages', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->uuid('liaisonrequest_id');
            $table->uuid('writer_id');
            $table->uuid('broker_id');
            $table->longText('message');
            $table->longText('user_id');
            
            $table->string('type') -> default('text');
            $table->dateTime('fetched_at') -> nullable();
            $table->dateTime('read_at') -> nullable();
            $table->timestamps();

            $table->index('liaisonrequest_id');
            $table->index('writer_id');
            $table->index('broker_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('requestmessages');
    }
}
