<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePreOrderArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pre_order_articles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('po_id')->nullable();
            $table->unsignedBigInteger('pr_id')->nullable();
            $table->foreign('po_id')->references('id')->on('pre_orders');
            $table->foreign('pr_id')->references('id')->on('products');
            $table->enum('poa_draft', ['0', '1']);
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
        Schema::dropIfExists('pre_order_articles');
    }
}
