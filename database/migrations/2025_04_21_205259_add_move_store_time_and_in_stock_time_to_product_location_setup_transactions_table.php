<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMoveStoreTimeAndInStockTimeToProductLocationSetupTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('product_location_setup_transactions', function (Blueprint $table) {
            $table->timestamp('move_store_time')->nullable()->after('updated_at');
            $table->timestamp('in_stock_time')->nullable()->after('move_store_time');
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
            $table->dropColumn(['move_store_time', 'in_stock_time']);
        });
    }
}
