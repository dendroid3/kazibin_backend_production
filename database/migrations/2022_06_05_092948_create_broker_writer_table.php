<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBrokerWriterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('broker_writer', function (Blueprint $table) {
            $table->uuid('broker_id');
            $table->uuid('writer_id');
            $table->integer('cost_per_page');

            $table->index('broker_id');
            $table->index('writer_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('broker_writer');
    }
}
