<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWarehouseIndexTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('warehouse_index', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('st_id');
            $table->string('w_code');

            $table->timestamps();
            $table->foreign('st_id')->references('id')->on('stores')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('warehouse_index');
    }
}
