<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTempTransaksiOnlineTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('online_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->string('order_status')->nullable();
            $table->text('reason_cancellation')->nullable();
            $table->string('no_resi')->nullable();
            $table->string('platform_name')->nullable();
            $table->string('shipping_method');
            $table->date('order_date_created');
            $table->date('payment_date')->nullable();
            $table->string('payment_method');
            $table->string('shipping_fee')->nullable();
            $table->string('total_payment');
            $table->string('city');
            $table->string('province');
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
        Schema::dropIfExists('online_transactions');
    }
}
