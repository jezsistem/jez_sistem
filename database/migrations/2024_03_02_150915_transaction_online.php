<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TransactionOnline extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_online', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('st_id')->nullable();
            $table->string('platform_type');
            $table->string('order_number');

            $table->string('pre_order')->nullable();
            $table->string('resi_number')->nullable();
            $table->string('shipping_method')->nullable();
            $table->dateTime('ship_deadline')->nullable();
            $table->dateTime('ship_delivery_date')->nullable();
            $table->dateTime('order_date_created')->nullable();

            $table->string('payment_method')->nullable();
            $table->string('SKU')->nullable();
            $table->string('original_price');
            $table->string('price_after_discount');
            $table->integer('quantity');

            $table->text('seller_note')->nullable();
            $table->string('total_price', 100)->nullable();
            $table->string('total_discount', 100)->nullable();
            $table->string('shipping_fee', 100)->nullable();
            $table->string('voucher_seller')->nullable();
            $table->string('cashback_coin', 100)->nullable();
            $table->string('voucher')->nullable();
            $table->string('voucher_platform')->nullable();
            $table->string('discount_seller', 100)->nullable();
            $table->string('discount_platform', 100)->nullable();
            $table->integer('shopee_coin_pieces')->nullable();
            $table->string('credit_card_discounts', 100)->nullable();
            $table->string('shipping_costs', 100)->nullable();
            $table->string('total_payment', 100)->nullable();
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->dateTime('order_complete_at')->nullable();
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
        Schema::dropIfExists('transaction_online');
    }
}
