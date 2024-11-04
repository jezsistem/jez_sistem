<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFlagsToTsProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('mp_best_seller')->default(false); // false untuk default
            $table->boolean('mp_stock_masking')->default(false);
            $table->boolean('complement')->default(false);
            $table->boolean('consignment')->default(false);
        });
    }
    


    /**
     * Reverse the migrations.
     *
     * @return void
     */
   
}
