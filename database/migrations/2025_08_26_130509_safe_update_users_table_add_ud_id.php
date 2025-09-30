<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class SafeUpdateUsersTableAddUdId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Check if ud_id column already exists
        if (Schema::hasColumn('users', 'ud_id')) {
            echo "Column ud_id already exists in users table, skipping column creation.\n";
        } else {
            echo "Adding ud_id column to users table.\n";
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedBigInteger('ud_id')->nullable()->after('stt_id');
            });
        }
        
        // Copy stt_id values to ud_id for existing records (safe to run multiple times)
        echo "Copying stt_id values to ud_id...\n";
        $updated = DB::statement('UPDATE ts_users SET ud_id = stt_id WHERE stt_id IS NOT NULL AND (ud_id IS NULL OR ud_id != stt_id)');
        echo "Updated records where ud_id was null or different from stt_id.\n";
        
        // Check if foreign key already exists
        $foreignKeyExists = collect(DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'ts_users' 
            AND COLUMN_NAME = 'ud_id'
            AND REFERENCED_TABLE_NAME IS NOT NULL
        "))->isNotEmpty();
        
        if ($foreignKeyExists) {
            echo "Foreign key for ud_id already exists, skipping creation.\n";
        } else {
            echo "Adding foreign key constraint for ud_id.\n";
            try {
                Schema::table('users', function (Blueprint $table) {
                    $table->foreign('ud_id')->references('id')->on('ts_store_types');
                });
                echo "Successfully added foreign key constraint.\n";
            } catch (\Exception $e) {
                echo "Warning: Could not add foreign key constraint: " . $e->getMessage() . "\n";
                echo "This might be due to data integrity issues. Please check manually.\n";
            }
        }
        
        echo "Migration completed successfully.\n";
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        echo "Rolling back ud_id changes...\n";
        
        try {
            // Drop foreign key if exists
            Schema::table('users', function (Blueprint $table) {
                $table->dropForeign(['ud_id']);
            });
            echo "Dropped foreign key constraint.\n";
        } catch (\Exception $e) {
            echo "Foreign key constraint not found or already dropped.\n";
        }
        
        try {
            // Drop column if exists
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('ud_id');
            });
            echo "Dropped ud_id column.\n";
        } catch (\Exception $e) {
            echo "Column ud_id not found or already dropped.\n";
        }
    }
}