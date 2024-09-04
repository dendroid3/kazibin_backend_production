<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->uuid('user_id');
            $table->string('mpesa_transaction_id') -> nullable();
            $table->uuid('bid_id') -> nullable();
            $table->uuid('service_id') -> nullable();
            $table->uuid('task_id') -> nullable();
            $table->uuid('account_id') -> nullable();
            $table->string('type');
            $table->string('description');
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
        Schema::dropIfExists('transactions');
    }
}
