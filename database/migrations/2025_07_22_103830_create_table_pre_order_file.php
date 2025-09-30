<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTablePreOrderFile extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pre_order_file', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pre_order_id');
            $table->foreign('pre_order_id')->references('id')->on('pre_orders')->onDelete('cascade');
            $table->string('file_pre_orders')->nullable();
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
        Schema::dropIfExists('pre_order_file');
    }
}
