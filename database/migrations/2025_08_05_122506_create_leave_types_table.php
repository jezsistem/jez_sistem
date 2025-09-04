<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeaveTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leave_types', function (Blueprint $table) {
            $table->id();
            $table->string('lt_code')->unique(); // Kode tipe cuti
            $table->string('lt_name'); // Nama tipe cuti
            $table->text('lt_description')->nullable(); // Deskripsi
            $table->integer('lt_default_days')->default(0); // Standar lama hari
            $table->integer('lt_default_hours')->default(0); // Standar lama jam
            $table->enum('lt_unit', ['days', 'hours'])->default('days'); // Unit (hari/jam)
            $table->boolean('lt_requires_approval')->default(true); // Perlu approval
            $table->boolean('lt_is_active')->default(true); // Status aktif
            $table->string('lt_color')->default('#3699FF'); // Warna untuk UI
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
        Schema::dropIfExists('leave_types');
    }
}
