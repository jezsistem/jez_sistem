<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNotesToProductMutationAndTempMutasiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_mutations', function (Blueprint $table) {
            $table->text('notes')->nullable()->after('pmt_qty');
        });

        Schema::table('temp_mutasi', function (Blueprint $table) {
            $table->text('notes')->nullable()->after('pls_qty');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_mutations', function (Blueprint $table) {
            $table->dropColumn('notes');
        });

        Schema::table('temp_mutasi', function (Blueprint $table) {
            $table->dropColumn('notes');
        });
    }
}
