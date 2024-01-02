<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPsRekeningPsNpwpToProductSuppliersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_suppliers', function (Blueprint $table) {
            // check column exist or not
            if (Schema::hasColumn('product_suppliers', 'ps_rekening')) {
                $table->dropColumn('ps_rekening');
            }

            if (Schema::hasColumn('product_suppliers', 'ps_npwp')) {
                $table->dropColumn('ps_npwp');
            }

            $table->string('ps_rekening')->nullable()->after('ps_description');
            $table->string('ps_npwp')->nullable()->after('ps_rekening');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_suppliers', function (Blueprint $table) {
            //
        });
    }
}
