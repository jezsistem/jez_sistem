<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePosTransactionDetailLogsTable extends Migration
{
    public function up()
    {
        Schema::create('pos_transaction_detail_logs', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('ptd_id');

            $table->decimal('ps_purchase_price', 15, 2)->nullable();
            $table->decimal('ps_price_tag', 15, 2)->nullable();
            $table->decimal('ps_sell_price', 15, 2)->nullable();
            $table->string('p_turnoverclass', 50)->nullable();

            $table->timestamps();

            $table->foreign('ptd_id')
                ->references('id')
                ->on('pos_transaction_details')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('pos_transaction_detail_logs');
    }
}
