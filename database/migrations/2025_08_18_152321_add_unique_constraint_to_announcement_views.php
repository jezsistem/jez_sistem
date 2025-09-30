<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUniqueConstraintToAnnouncementViews extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('announcement_views', function (Blueprint $table) {
            // Add unique constraint to prevent duplicate views
            // We'll keep the existing index and add unique constraint
            $table->unique(['announcement_id', 'user_id'], 'announcement_views_unique_user_announcement');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('announcement_views', function (Blueprint $table) {
            // Remove unique constraint
            $table->dropUnique('announcement_views_unique_user_announcement');
        });
    }
}
