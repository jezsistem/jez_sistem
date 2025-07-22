<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnPoTypeInPreOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pre_orders', function (Blueprint $table) {
            $table->tinyInteger('po_type')->nullable()->after('ps_id');
            $table->text('preorder_description')->nullable()->after('po_draft');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pre_orders', function (Blueprint $table) {
            $table->dropColumn(['po_type, preorder_description']);
        });
    }
}
