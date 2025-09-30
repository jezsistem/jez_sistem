<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnnouncementUserReactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('announcement_user_reactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('announcement_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('reaction_id');
            $table->timestamps();
            
            $table->foreign('announcement_id')->references('id')->on('announcements')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('reaction_id')->references('id')->on('announcement_reactions');
            
            // One reaction per user per announcement
            $table->unique(['announcement_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('announcement_user_reactions');
    }
}
