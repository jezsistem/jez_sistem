<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifySaIdForSetNullInProductLocations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_locations', function (Blueprint $table) {
            $table->dropForeign(['sa_id']);
            $table->foreign('sa_id')->references('id')->on('storage_areas')->onDelete('set null')->change();
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
            $table->dropForeign(['sa_id']);
            $table->foreign('sa_id')->references('id')->on('storage_areas')->onDelete('cascade');
        });
    }
}
