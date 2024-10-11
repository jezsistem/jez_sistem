<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePreOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pre_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('st_id')->nullable();
            $table->unsignedBigInteger('br_id')->nullable();
            $table->unsignedBigInteger('ss_id')->nullable();
            $table->unsignedBigInteger('ps_id')->nullable();
            $table->foreign('st_id')->references('id')->on('stores');
            $table->foreign('br_id')->references('id')->on('brands');
            $table->foreign('ss_id')->references('id')->on('seasons');
            $table->foreign('ps_id')->references('id')->on('product_suppliers');
            $table->string('pre_order_code');
            $table->enum('po_delete', ['0', '1']);
            $table->enum('po_draft', ['0', '1']);
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
        Schema::dropIfExists('pre_orders');
    }
}
