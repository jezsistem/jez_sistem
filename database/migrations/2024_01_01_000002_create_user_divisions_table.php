<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserDivisionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_divisions', function (Blueprint $table) {
            $table->id();
            $table->string('ud_name'); // Nama divisi (FINANCE & TECH, GA & CS, HELPER MLG, HR MLG & HRGA SBY, LOG MLG, AMP MLG, AMP SBY)
            $table->string('ud_code', 20)->unique(); // Kode divisi
            $table->text('ud_description')->nullable(); // Deskripsi divisi
            $table->enum('ud_status', ['active', 'inactive'])->default('active');
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
        Schema::dropIfExists('user_divisions');
    }
} 