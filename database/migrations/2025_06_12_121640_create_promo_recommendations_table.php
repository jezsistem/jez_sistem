<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromoRecommendationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promo_recommendations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('p_id')->nullable();
            $table->enum('channel', ['ONLINE', 'OFFLINE'])->nullable();
            $table->float('discount')->nullable();
            $table->string('notes')->nullable();
            $table->timestamps();

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
        Schema::dropIfExists('promo_recommendations');
    }
}
