<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnnouncementReactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('announcement_reactions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Planned, In Progress, Cancel, Done, OK
            $table->string('emoji', 10)->nullable(); // 📅, ⏳, ❌, ✅, 👍
            $table->string('color', 7)->default('#6c757d'); // Hex color code
            $table->boolean('hide_announcement')->default(false); // Hide announcement after this reaction
            $table->integer('sort_order')->default(0);
            $table->enum('status', ['active', 'inactive'])->default('active');
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
        Schema::dropIfExists('announcement_reactions');
    }
}
