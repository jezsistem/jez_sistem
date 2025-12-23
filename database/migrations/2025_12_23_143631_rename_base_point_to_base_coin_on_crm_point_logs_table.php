<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameBasePointToBaseCoinOnCrmPointLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('crm_point_logs', function (Blueprint $table) {
            $table->renameColumn('base_point', 'base_coin');
        });
    }

    public function down(): void
    {
        Schema::table('crm_point_logs', function (Blueprint $table) {
            $table->renameColumn('base_coin', 'base_point');
        });
    }
}
