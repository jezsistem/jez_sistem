<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTsUserPositionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_positions', function (Blueprint $table) {
            $table->id();
            $table->string('up_code')->unique(); // Kode jabatan
            $table->string('up_name'); // Nama jabatan
            $table->text('up_description')->nullable(); // Deskripsi jabatan
            $table->integer('up_level')->default(1); // Level jabatan (1=Staff, 2=Supervisor, 3=Manager, 4=Director)
            $table->boolean('up_can_approve_leave')->default(false); // Bisa approve leave
            $table->boolean('up_is_active')->default(true); // Status aktif
            $table->string('up_color')->default('#3699FF'); // Warna untuk UI
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
        Schema::dropIfExists('user_positions');
    }
}
