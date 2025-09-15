<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateTsUserDivisionsView extends Migration
{
    public function up()
    {
        DB::statement("
        CREATE OR REPLACE VIEW ts_user_divisions AS
        SELECT 
            ts_store_types.id AS id,
            ts_store_types.stt_name AS ud_name,
            UCASE(SUBSTR(REPLACE(REPLACE(REPLACE(ts_store_types.stt_name, ' ', ''), '&', ''), '-', ''), 1, 10)) AS ud_code,
            ts_store_types.stt_description AS ud_description,
            CASE 
                WHEN ts_store_types.stt_delete = '0' THEN 'active' 
                ELSE 'inactive' 
            END AS ud_status,
            ts_store_types.lead_id AS lead_id,
            ts_store_types.manager_id AS manager_id,
            ts_store_types.created_by AS created_by,
            ts_store_types.updated_by AS updated_by,
            ts_store_types.created_at AS created_at,
            ts_store_types.updated_at AS updated_at
        FROM ts_store_types
        WHERE ts_store_types.stt_delete IN ('0','1');
    ");
    }

    public function down()
    {
        // balikin ke versi lama kalau perlu
        DB::statement("
        CREATE OR REPLACE VIEW ts_user_divisions AS
        SELECT 
            ts_store_types.id AS id,
            ts_store_types.stt_name AS ud_name,
            UCASE(SUBSTR(REPLACE(REPLACE(REPLACE(ts_store_types.stt_name, ' ', ''), '&', ''), '-', ''), 1, 10)) AS ud_code,
            ts_store_types.stt_description AS ud_description,
            CASE 
                WHEN ts_store_types.stt_delete = '0' THEN 'active' 
                ELSE 'inactive' 
            END AS ud_status,
            ts_store_types.created_by AS created_by,
            ts_store_types.updated_by AS updated_by,
            ts_store_types.created_at AS created_at,
            ts_store_types.updated_at AS updated_at
        FROM ts_store_types
        WHERE ts_store_types.stt_delete IN ('0','1');
    ");
    }
}
