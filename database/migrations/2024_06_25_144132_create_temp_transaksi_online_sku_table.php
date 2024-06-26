<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTempTransaksiOnlineSkuTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('online_transaction_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('to_id');
            $table->string('order_number');
            $table->string('sku');
            $table->integer('qty');
            $table->integer('return_qty')->nullable();
            $table->string('original_price');
            $table->string('discount_seller')->nullable();
            $table->string('discount_platform')->nullable();
            $table->string('total_discount');
            $table->string('price_after_discount');
            $table->timestamps();

            $table->foreign('to_id')->references('id')->on('online_transactions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('online_transaction_details', function (Blueprint $table) {
            $table->dropForeign(['to_id']);
        });

        Schema::dropIfExists('online_transaction_details');
    }
}
