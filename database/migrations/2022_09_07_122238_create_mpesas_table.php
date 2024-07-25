<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMpesasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mpesas', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->string('checkout_request_id');
            $table->uuid('user_id');
            $table->bigInteger('paying_phone_number');
            $table->string('receipt_number')->nullable();
            $table->dateTime('transaction_date')->nullable();
            $table->integer('status')->default(0);
            $table->integer('amount');
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
        Schema::dropIfExists('mpesas');
    }
}
