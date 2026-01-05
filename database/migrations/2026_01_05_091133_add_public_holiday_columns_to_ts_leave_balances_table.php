<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPublicHolidayColumnsToTsLeaveBalancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('leave_balances', function (Blueprint $table) {

            // Jumlah PH awal (total PH tahun berjalan)
            $table->integer('lb_initial_ph')
                ->default(0)
                ->after('lb_remaining_balance');

            // Jumlah PH yang sudah terpakai
            $table->integer('lb_ph_used')
                ->default(0)
                ->after('lb_initial_ph');

            // Sisa PH
            $table->integer('lb_ph_remaining')
                ->default(0)
                ->after('lb_ph_used');

        });
    }


    public function down(): void
    {
        Schema::table('leave_balances', function (Blueprint $table) {

            $table->dropColumn([
                'lb_initial_ph',
                'lb_ph_used',
                'lb_ph_remaining',
            ]);

        });
    }
}
