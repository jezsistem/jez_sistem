<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class FixUserDivisionsCollation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Drop existing view
        try {
            DB::statement("DROP VIEW IF EXISTS ts_user_divisions");
            echo "Dropped existing ts_user_divisions view.\n";
        } catch (\Exception $e) {
            echo "Error dropping view: " . $e->getMessage() . "\n";
        }
        
        // Recreate view with proper collation
        try {
            DB::statement("
                CREATE VIEW ts_user_divisions AS
                SELECT 
                    id,
                    CONVERT(stt_name USING utf8mb4) COLLATE utf8mb4_unicode_ci as ud_name,
                    UPPER(SUBSTRING(REPLACE(REPLACE(REPLACE(CONVERT(stt_name USING utf8mb4) COLLATE utf8mb4_unicode_ci, ' ', ''), '&', ''), '-', ''), 1, 10)) COLLATE utf8mb4_unicode_ci as ud_code,
                    CONVERT(stt_description USING utf8mb4) COLLATE utf8mb4_unicode_ci as ud_description,
                    CASE WHEN stt_delete = '0' THEN 'active' ELSE 'inactive' END COLLATE utf8mb4_unicode_ci as ud_status,
                    created_by,
                    updated_by,
                    created_at,
                    updated_at
                FROM ts_store_types
                WHERE stt_delete IN ('0', '1')
            ");
            echo "Successfully recreated ts_user_divisions view with proper collation.\n";
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
    }
}
