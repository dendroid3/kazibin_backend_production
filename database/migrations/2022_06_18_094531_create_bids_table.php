<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBidsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bids', function (Blueprint $table) {
            $table->uuid('id') -> primary() -> unique();
            //1 = Unresolved, 2 = Pulled, 3 = Rejected, 4 = Won, 5 = Lost
            $table->integer('status')->default(1);
            $table->uuid('task_id');
            $table->uuid('broker_id');
            $table->uuid('writer_id');
            $table->timestamps();

            $table->index('task_id');
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
        Schema::dropIfExists('bids');
    }
}
