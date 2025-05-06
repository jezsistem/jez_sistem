<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOnlineFundsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('online_funds', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('st_id')->nullable();

            $table->string('platform_name')->collation('utf8mb4_general_ci');
            $table->string('order_number')->collation('utf8mb4_general_ci');
            $table->double('total_disburshed_amount');
            $table->double('final_price');
            $table->double('total_online_cut');

            $table->double('seller_voucher_discount')->nullable();
            $table->double('affiliate_cut')->nullable();
            $table->double('marketplace_commision_fee')->nullable();
            $table->double('service_fee')->nullable();
            $table->double('voucher_xtra_service_fee')->nullable();
            $table->double('cashback_service_fee')->nullable();

            $table->date('cashout_date')->nullable();
            $table->date('transaction_date')->nullable();
            $table->timestamps();

            $table->foreign('st_id')->references('id')->on('stores');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('online_funds');
    }
}
