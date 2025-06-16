<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSaIdToProductLocationSetupTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_location_setup_transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('sa_id')->after('id')->nullable();

            // Optional: jika ada relasi ke storage_areas
            $table->foreign('sa_id')->references('id')->on('storage_areas')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_location_setup_transactions', function (Blueprint $table) {
            $table->dropForeign(['sa_id']);
            $table->dropColumn('sa_id');
        });
    }
}
