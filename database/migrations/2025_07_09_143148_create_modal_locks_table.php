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

            // Data apa yang dikunci? (Ini bagian penting untuk skalabilitas)
            $table->unsignedBigInteger('lockable_id');
            $table->string('lockable_type');

            // Kapan lock ini kedaluwarsa?
            $table->timestamp('expires_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('modal_locks');
    }
};
