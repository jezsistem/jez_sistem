<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDiscountedPriceAndPriceDiffToArticlesPromoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('articles_promo', function (Blueprint $table) {
            $table->bigInteger('discounted_price')->default(0);
            $table->bigInteger('price_diff')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('articles_promo', function (Blueprint $table) {
            $table->dropColumn('discounted_price');
            $table->dropColumn('price_diff');
        });
    }
}
