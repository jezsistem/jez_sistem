<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMaErrorFilesToMassAdjustmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('mass_adjustments', function (Blueprint $table) {
            $table->string('ma_proof_file')->nullable()->after('tipe_adjustment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mass_adjustments', function (Blueprint $table) {
            $table->dropColumn('ma_proof_file');
        });
    }
}
