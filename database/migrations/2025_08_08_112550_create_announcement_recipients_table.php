<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnnouncementRecipientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('announcement_recipients', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('announcement_id');
            $table->enum('recipient_type', ['user', 'division']); // individual user or division
            $table->unsignedBigInteger('recipient_id'); // user_id or division_id
            $table->timestamps();
            
            $table->foreign('announcement_id')->references('id')->on('announcements')->onDelete('cascade');
            $table->index(['announcement_id', 'recipient_type', 'recipient_id'], 'ann_recipients_lookup_idx');
            
            // Prevent duplicate recipients
            $table->unique(['announcement_id', 'recipient_type', 'recipient_id'], 'ann_recipients_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('announcement_recipients');
    }
}
