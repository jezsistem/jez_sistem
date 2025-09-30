<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnnouncementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('created_by');
            $table->enum('target_type', ['all', 'division', 'individual'])->default('all');
            $table->boolean('is_pinned')->default(false);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            
            // Foreign keys will be added later after all tables are created
            $table->index(['target_type', 'status', 'published_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('announcements');
    }
}
