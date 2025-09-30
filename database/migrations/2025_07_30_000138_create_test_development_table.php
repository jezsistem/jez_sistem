<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTestDevelopmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('test_development', function (Blueprint $table) {
            $table->id(); // Kolom id (primary key, auto increment)
            $table->string('name'); // Kolom name (varchar)
            $table->text('description')->nullable(); // Kolom description (bisa null)
            $table->timestamps(); // Kolom created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('test_development');
    }
}
