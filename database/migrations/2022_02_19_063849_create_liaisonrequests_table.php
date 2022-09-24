<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLiaisonrequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('liaisonrequests', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->longText('initiator_id');
            $table->integer('cost_per_page')->nullable();
            $table->string('pay_day')->nullable();
            $table->integer('status')->default(1);
            $table->uuid('broker_id')->nullable();
            $table->uuid('writer_id')->nullable();
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
        Schema::dropIfExists('liaisonrequests');
    }
}
