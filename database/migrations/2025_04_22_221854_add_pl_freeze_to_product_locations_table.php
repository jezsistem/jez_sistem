<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPlFreezeToProductLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_locations', function (Blueprint $table) {
            $table->boolean('pl_freeze')->after('pl_delete')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_locations', function (Blueprint $table) {
            $table->dropColumn('pl_freeze');
        });
    }
}
