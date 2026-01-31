<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('p_id')->index();
            $table->unsignedBigInteger('pst_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->foreign('p_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('pst_id')->references('id')->on('product_stocks')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->enum('levels', ['article', 'sku']);

            $table->string('target_column', 100)->nullable()->default('text');
            $table->string('source', 100)->nullable()->default('text');
            $table->string('data_before')->nullable();
            $table->string('data_after')->nullable();
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
        Schema::dropIfExists('product_logs');
    }
}
