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
            $table->string('first_name');
            $table->string('middle_name')->default('Doe');
            $table->string('last_name')->default('Doe');
            $table->uuid('user_id')->nullable();
            $table->integer('msisdn');
            $table->string('bill_ref_number');
            $table->string('mpesa_transaction_id');
            $table->dateTime('transation_time');
            $table->integer('status')->default(1);
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
