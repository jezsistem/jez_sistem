<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveryReceiptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_receipts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dr_id'); // relasi ke delivery_receipt_confirmations
            $table->string('resi')->unique();
            $table->string('marketplace_name')->nullable();
            $table->integer('item_qty')->default(0);
            $table->string('city_destinations')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->foreign('dr_id')
                ->references('id')
                ->on('delivery_recaps')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('delivery_receipts');
    }
}
