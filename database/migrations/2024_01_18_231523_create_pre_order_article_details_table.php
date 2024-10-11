<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePreOrderArticleDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pre_order_article_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('poa_id')->nullable();
            $table->unsignedBigInteger('pst_id')->nullable();
            $table->foreign('poa_id')->references('id')->on('pre_order_articles');
            $table->foreign('pst_id')->references('id')->on('product_stocks');
            $table->double('poad_qty')->nullable();
            $table->double('poad_purchase_price')->nullable();
            $table->double('poad_total_price')->nullable();
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
        Schema::dropIfExists('pre_order_article_details');
    }
}
