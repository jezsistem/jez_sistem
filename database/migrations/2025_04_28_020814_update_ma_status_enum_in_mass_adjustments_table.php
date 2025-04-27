<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateMaStatusEnumInMassAdjustmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mass_adjustments', function (Blueprint $table) {
            // Hapus dulu kolom enum
            $table->dropColumn('ma_status');
        });

        Schema::table('mass_adjustments', function (Blueprint $table) {
            // Tambahkan kolom baru sebagai integer
            $table->integer('ma_status')->default(0)->after('ma_executor');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mass_adjustments', function (Blueprint $table) {
            // Hapus kolom integer
            $table->dropColumn('ma_status');
        });

        Schema::table('mass_adjustments', function (Blueprint $table) {
            // Balikin ke enum
            $table->enum('ma_status', ['0', '1'])->default('0')->after('ma_executor');
        });
    }
}
