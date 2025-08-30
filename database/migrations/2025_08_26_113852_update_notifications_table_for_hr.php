<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdateNotificationsTableForHr extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Check which columns exist and add only missing ones
        $columns = collect(DB::select("DESCRIBE ts_notifications"))->pluck('Field');
        
        Schema::table('notifications', function (Blueprint $table) use ($columns) {
            if (!$columns->contains('ud_id')) {
                $table->unsignedBigInteger('ud_id')->nullable()->after('stt_id');
            }
            if (!$columns->contains('user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('ud_id');
            }
            if (!$columns->contains('type')) {
                $table->string('type')->default('system')->after('message');
            }
            if (!$columns->contains('data')) {
                $table->json('data')->nullable()->after('type');
            }
        });
        
        // Add foreign keys and indexes if they don't exist
        Schema::table('notifications', function (Blueprint $table) {
            // Check if foreign keys exist before adding
            $foreignKeys = collect(DB::select("
                SELECT CONSTRAINT_NAME 
                FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'ts_notifications' 
                AND REFERENCED_TABLE_NAME IS NOT NULL
            "))->pluck('CONSTRAINT_NAME');
            
            if (!$foreignKeys->contains('ts_notifications_ud_id_foreign')) {
                $table->foreign('ud_id')->references('id')->on('store_types');
            }
            if (!$foreignKeys->contains('ts_notifications_user_id_foreign')) {
                $table->foreign('user_id')->references('id')->on('users');
            }
            
            // Add indexes
            $indexes = collect(DB::select("SHOW INDEX FROM ts_notifications"))->pluck('Key_name');
            
            if (!$indexes->contains('ts_notifications_ud_id_is_read_index')) {
                $table->index(['ud_id', 'is_read']);
            }
            if (!$indexes->contains('ts_notifications_user_id_is_read_index')) {
                $table->index(['user_id', 'is_read']);
            }
            if (!$indexes->contains('ts_notifications_type_is_read_index')) {
                $table->index(['type', 'is_read']);
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notifications', function (Blueprint $table) {
            // Drop foreign keys if they exist
            try {
                $table->dropForeign(['ud_id']);
            } catch (Exception $e) {
                // Foreign key doesn't exist
            }
            try {
                $table->dropForeign(['user_id']);
            } catch (Exception $e) {
                // Foreign key doesn't exist
            }
            
            // Drop indexes
            try {
                $table->dropIndex(['ud_id', 'is_read']);
                $table->dropIndex(['user_id', 'is_read']);
                $table->dropIndex(['type', 'is_read']);
            } catch (Exception $e) {
                // Indexes don't exist
            }
            
            // Drop columns if they exist
            $columns = collect(DB::select("DESCRIBE ts_notifications"))->pluck('Field');
            
            if ($columns->contains('data')) {
                $table->dropColumn('data');
            }
            if ($columns->contains('type')) {
                $table->dropColumn('type');
            }
            if ($columns->contains('user_id')) {
                $table->dropColumn('user_id');
            }
            if ($columns->contains('ud_id')) {
                $table->dropColumn('ud_id');
            }
        });
    }
}