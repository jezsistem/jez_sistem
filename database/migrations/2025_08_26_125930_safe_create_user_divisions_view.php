<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class SafeCreateUserDivisionsView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Check if view already exists
        $viewExists = collect(DB::select("
            SELECT table_name 
            FROM information_schema.views 
            WHERE table_schema = DATABASE() 
            AND table_name = 'ts_user_divisions'
        "))->isNotEmpty();
        
        if ($viewExists) {
            echo "View ts_user_divisions already exists, skipping creation.\n";
            return;
        }
        
        // Check if table exists and drop it
        if (Schema::hasTable('user_divisions')) {
            echo "Dropping existing user_divisions table to create view.\n";
            
            // Drop foreign keys first
            $foreignKeys = [
                'ts_daily_schedules_ud_id_foreign' => 'ts_daily_schedules',
                'ts_users_ud_id_foreign' => 'ts_users'
            ];
            
            foreach ($foreignKeys as $constraint => $table) {
                try {
                    DB::statement("ALTER TABLE {$table} DROP FOREIGN KEY {$constraint}");
                    echo "Dropped foreign key: {$constraint}\n";
                } catch (\Exception $e) {
                    echo "Foreign key {$constraint} not found or already dropped.\n";
                }
            }
            
            Schema::dropIfExists('user_divisions');
        }
        
        // Create view
        try {
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
            echo "Successfully created ts_user_divisions view.\n";
        } catch (\Exception $e) {
            echo "Error creating view: " . $e->getMessage() . "\n";
            throw $e;
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        try {
            DB::statement("DROP VIEW IF EXISTS ts_user_divisions");
            echo "Dropped ts_user_divisions view.\n";
        } catch (\Exception $e) {
            echo "Error dropping view: " . $e->getMessage() . "\n";
        }
        
        // Recreate original table if needed
        if (!Schema::hasTable('user_divisions')) {
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
            echo "Recreated user_divisions table.\n";
        }
    }
}