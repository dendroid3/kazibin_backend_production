<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->uuid('user_id');
            $table->string('code');
            $table->string('title');
            $table->string('type');
            $table->string('profile_origin');
            $table->string('profile_gender');
            $table->integer('total_orders');
            $table->integer('pending_orders');
            $table->integer('amount_earned');
            $table->integer('cost');
            $table->string('rating');
            $table->boolean('negotiable')->default(true);
            $table->boolean('display')->dafault(true);
            $table->dateTime('expiry');
            $table->longText('description')->nullable();
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
        Schema::dropIfExists('accounts');
    }
}
