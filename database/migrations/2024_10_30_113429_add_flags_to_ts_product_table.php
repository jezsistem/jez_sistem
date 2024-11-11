<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFlagsToTsProductTable extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['mp_best_seller', 'mp_stock_masking', 'complement', 'consignment']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    // public function down()
    // {
    //     Schema::table('products', function (Blueprint $table) {
    //         $table->tinyInteger('mp_best_seller')->default(0);
    //         $table->tinyInteger('mp_stock_masking')->default(0);
    //         $table->tinyInteger('complement')->default(0);
    //         $table->tinyInteger('consignment')->default(0);
    //     });
    // }
}
