<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveRouteFromPositionAccessTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('position_access', function (Blueprint $table) {
            $table->dropColumn('route');
        });
    }

    /**
     * Rollback migrasi.
     */
    public function down(): void
    {
        Schema::table('position_access', function (Blueprint $table) {
            $table->string('route')->nullable(); // sesuaikan tipe data kalau sebelumnya bukan string
        });
    }
}
