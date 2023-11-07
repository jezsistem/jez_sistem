<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNoSymbolArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('no_symbol_articles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pst_id');
            $table->foreign('pst_id')->references('id')->on('product_stocks');
            $table->string('brand')->nullable();
            $table->string('name')->nullable();
            $table->string('color')->nullable();
            $table->string('size')->nullable();
            $table->string('fullname')->nullable();
            $table->string('brandname')->nullable();
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
        Schema::dropIfExists('no_symbol_articles');
    }
}
