<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionOnlineDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_online_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('to_id');
            $table->text('seller_note')->nullable();
            $table->string('order_status');
            $table->string('order_sub_status');
            $table->string('cancel_type')->nullable();
            $table->string('cancel_by')->nullable();
            $table->string('reason_cancellation')->nullable();
            $table->dateTime('payment_date')->nullable();
            $table->integer('return_quantity')->nullable();
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->timestamps();

            $table->foreign('to_id')->references('id')->on('transaction_online')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transaction_online_details');
    }
}
