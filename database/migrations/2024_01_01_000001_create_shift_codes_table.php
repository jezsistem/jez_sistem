<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShiftCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shift_codes', function (Blueprint $table) {
            $table->id();
            $table->string('sc_code', 10)->unique(); // Kode shift (S, N/A, LPH, LL, L, I, FSE, FS2, FS1, FS0, FM2, FM1, PFM, PF0, PF, SM2, SM1, PS2, PS1, PS0, PL2, PL1)
            $table->string('sc_description'); // Keterangan shift
            $table->string('sc_shift_name'); // Nama shift (Sakit, Belum Disetting, Libur Tgl Merah, Libur Cuti, Libur, Izin, Shift 1, Shift 2, Shift 0, Full, Full Shift 0)
            $table->time('sc_start_time')->nullable(); // Waktu mulai shift (HH:MM)
            $table->time('sc_end_time')->nullable(); // Waktu berakhir shift (HH:MM)
            $table->enum('sc_type', ['ALL', 'Full Time', 'Part Full', 'Part Time']); // Tipe shift
            $table->enum('sc_status', ['active', 'inactive'])->default('active');
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shift_codes');
    }
} 