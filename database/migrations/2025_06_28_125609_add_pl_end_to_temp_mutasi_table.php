<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPlEndToTempMutasiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('temp_mutasi', function (Blueprint $table) {
            $table->bigInteger('pl_end')->after('pls_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('temp_mutasi', function (Blueprint $table) {
            $table->dropColumn('pl_end');
        });
    }
}
