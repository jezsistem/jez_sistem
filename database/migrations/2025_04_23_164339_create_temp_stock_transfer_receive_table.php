<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTempStockTransferReceiveTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('temp_stock_transfer_receive', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stf_id')->nullable();
            $table->unsignedBigInteger('stfd_id')->nullable();
            $table->unsignedBigInteger('u_id');
            $table->integer('stfds_qty')->nullable();
            $table->timestamps();

            $table->foreign('stf_id')->references('id')->on('stock_transfers');
            $table->foreign('stfd_id')->references('id')->on('stock_transfer_details');
            $table->foreign('u_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('temp_stock_transfer_receive');
    }
}
