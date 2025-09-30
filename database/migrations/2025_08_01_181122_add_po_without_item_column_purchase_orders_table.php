<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPoWithoutItemColumnPurchaseOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->bigInteger('po_total_purchase')->default(0)->after('po_sub_discount');
            $table->bigInteger('po_total_qty')->default(0)->after('po_total_purchase');
            $table->bigInteger('po_payment_amount')->default(0)->after('po_total_qty');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropColumn(['po_total_purchase', 'po_total_qty','po_payment_amount']);
        });
    }
}
