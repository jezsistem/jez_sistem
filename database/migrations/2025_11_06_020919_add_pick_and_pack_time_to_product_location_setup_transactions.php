<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPickAndPackTimeToProductLocationSetupTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_location_setup_transactions', function (Blueprint $table) {
            $table->timestamp('pick_time')->nullable()->after('u_id_helper');
            $table->timestamp('pack_time')->nullable()->after('u_id_packer');
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
            //
        });
    }
}
