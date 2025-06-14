<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromoRecommendationDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promo_recommendation_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pr_id')->nullable();
            $table->unsignedBigInteger('p_id')->nullable();
            $table->float('discount')->nullable();
            $table->string('notes')->nullable();
            $table->timestamps();

            $table->foreign('pr_id')->references('id')->on('promo_recommendations')->onDelete('cascade');
            $table->foreign('p_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('promo_recommendation_details');
    }
}
