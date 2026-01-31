<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyEnumColumnPlstStatusInProductLocationSetupTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_location_setup_transactions', function (Blueprint $table) {
            DB::statement("ALTER TABLE ts_product_location_setup_transactions MODIFY COLUMN plst_status ENUM('INSTOCK', 'WAITING OFFLINE', 'WAITING ONLINE', 'WAITING FOR PACKING', 'DONE', 'REFUND', 'REJECT', 'WAITING FOR CHECKOUT', 'WAITING TO TAKE', 'DRAFT OFFLINE', 'WAITING FOR NAMESET', 'COMPLAINT', 'EXCHANGE', 'INSTOCK APPROVAL', 'DONE AMP', 'WAITING DISPLAY','UNDER REVIEW', 'WAITING RECEIPT','WAITING PACKING') NULL");
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
            DB::statement("ALTER TABLE ts_product_location_setup_transactions MODIFY COLUMN plst_status ENUM('INSTOCK', 'WAITING OFFLINE', 'WAITING ONLINE', 'WAITING FOR PACKING', 'DONE', 'REFUND', 'REJECT', 'WAITING FOR CHECKOUT', 'WAITING TO TAKE', 'DRAFT OFFLINE', 'WAITING FOR NAMESET', 'COMPLAINT', 'EXCHANGE', 'INSTOCK APPROVAL', 'DONE AMP', 'WAITING DISPLAY') NULL");
        });
    }
}
