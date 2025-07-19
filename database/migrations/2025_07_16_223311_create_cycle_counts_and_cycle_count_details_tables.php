<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCycleCountsAndCycleCountDetailsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cycle_counts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('st_id');
            $table->unsignedBigInteger('pl_id');
            $table->string('ccn_number')->unique();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('executor_by');
            $table->timestamps(); // created_at & updated_at
        });

        // Tabel detail: cycle_count_details
        Schema::create('cycle_count_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cc_id');
            $table->unsignedBigInteger('pst_id');
            $table->integer('qty_scan')->default(0);
            $table->string('cc_type');
            $table->string('diff_type');
            $table->integer('diff_qty')->default(0);
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            // Foreign key optional (jika kamu ingin enforce relasi)
            $table->foreign('cc_id')->references('id')->on('cycle_counts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cycle_count_details');
        Schema::dropIfExists('cycle_counts');
    }
}
