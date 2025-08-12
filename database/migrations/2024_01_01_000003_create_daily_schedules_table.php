<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDailySchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('daily_schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('ud_id')->nullable(); // User Division ID
            $table->unsignedBigInteger('sc_id'); // Shift Code ID
            $table->date('ds_date'); // Tanggal jadwal
            $table->time('ds_start_time')->nullable(); // Waktu mulai shift untuk hari tersebut
            $table->time('ds_end_time')->nullable(); // Waktu berakhir shift untuk hari tersebut
            $table->enum('ds_status', ['scheduled', 'completed', 'absent', 'late', 'early_leave'])->default('scheduled');
            $table->text('ds_notes')->nullable(); // Catatan tambahan
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('ud_id')->references('id')->on('user_divisions')->onDelete('set null');
            $table->foreign('sc_id')->references('id')->on('shift_codes')->onDelete('restrict');

            // Index untuk optimasi query
            $table->index(['user_id', 'ds_date']);
            $table->index(['ds_date']);
            $table->index(['ud_id', 'ds_date']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('daily_schedules');
    }
} 