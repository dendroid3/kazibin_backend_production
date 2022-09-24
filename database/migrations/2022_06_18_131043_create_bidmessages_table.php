<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBidmessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bidmessages', function (Blueprint $table) { 
            $table->uuid('id') -> primary() -> unique();
            $table->uuid('user_id');
            $table->uuid('bid_id');
            $table->string('type')->default('text');
            $table->dateTime('delivered_at') -> nullable();
            $table->dateTime('read_at') -> nullable();
            $table->longText('message');
            $table->timestamps();

            $table->index('user_id');
            $table->index('bid_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bidmessages');
    }
}
