<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateManagedaccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('managedaccounts', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->uuid('user_id')->index();
            $table->string('code')->unique();
            $table->string('status')->default('pending');
            $table->string('provider');
            $table->string('email');
            $table->string('provider_identifier')->nullable();
            $table->string('tasker_id')->nullable();
            $table->string('tasker_rate')->nullable();
            $table->string('owner_rate')->nullable();
            $table->string('jobraq_rate')->nullable();
            $table->string('proxy')->nullable();
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
        Schema::dropIfExists('managedaccounts');
    }
}
