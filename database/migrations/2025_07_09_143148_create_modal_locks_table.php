<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('modal_locks', function (Blueprint $table) {
            $table->id();
            
            // Siapa yang mengunci?
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // --- Bagian Kunci untuk Lock Spesifik ---
            // Data apa yang dikunci?
            $table->unsignedBigInteger('lockable_id');
            $table->string('lockable_type');
            // Pengenal unik untuk modal/URL
            $table->string('identifier'); 
            
            // Kapan lock ini kedaluwarsa?
            $table->timestamp('expires_at');
            $table->timestamps();

            // Index unik untuk memastikan satu sesi hanya bisa di-lock satu kali
            $table->unique(['lockable_id', 'lockable_type', 'identifier'], 'lock_unique_identifier');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('modal_locks');
    }
};
