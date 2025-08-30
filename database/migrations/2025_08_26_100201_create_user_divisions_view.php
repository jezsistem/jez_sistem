<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateUserDivisionsView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Check if view already exists
        $viewExists = DB::select("
            SELECT COUNT(*) as count 
            FROM information_schema.views 
            WHERE table_schema = DATABASE() 
            AND table_name = 'ts_user_divisions'
        ");
        
        if ($viewExists[0]->count > 0) {
            \Log::info("View ts_user_divisions already exists, skipping creation");
            return;
        }
        
        // Check if table exists
        $tableExists = Schema::hasTable('user_divisions');
        
        if ($tableExists) {
            // First, drop foreign key constraints that reference user_divisions
            $foreignKeys = [
                'ts_daily_schedules_ud_id_foreign' => 'ts_daily_schedules',
                'ts_users_ud_id_foreign' => 'ts_users'
            ];
            
            foreach ($foreignKeys as $constraint => $table) {
                try {
                    DB::statement("ALTER TABLE {$table} DROP FOREIGN KEY {$constraint}");
                } catch (\Exception $e) {
                    // Ignore if foreign key doesn't exist
                    \Log::info("Foreign key {$constraint} not found or already dropped: " . $e->getMessage());
                }
            }
            
            // Drop existing user_divisions table if exists
            Schema::dropIfExists('user_divisions');
        }
        
        // Create a view that maps store_types to user_divisions structure
        DB::statement("
            CREATE VIEW ts_user_divisions AS
            SELECT 
                id,
                stt_name as ud_name,
                UPPER(SUBSTRING(REPLACE(REPLACE(REPLACE(stt_name, ' ', ''), '&', ''), '-', ''), 1, 10)) as ud_code,
                stt_description as ud_description,
                CASE WHEN stt_delete = '0' THEN 'active' ELSE 'inactive' END as ud_status,
                created_by,
                updated_by,
                created_at,
                updated_at
            FROM ts_store_types
            WHERE stt_delete IN ('0', '1')
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop the view
        DB::statement("DROP VIEW IF EXISTS ts_user_divisions");
        
        // Recreate the original user_divisions table
        Schema::create('user_divisions', function (Blueprint $table) {
            $table->id();
            $table->string('ud_name');
            $table->string('ud_code', 20)->unique();
            $table->text('ud_description')->nullable();
            $table->enum('ud_status', ['active', 'inactive'])->default('active');
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();
        });
    }
}
