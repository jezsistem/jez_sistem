<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTsExternalAssignmentRequestDetailsTable extends Migration
{
    public function up(): void
    {
        Schema::table('external_assignment_request_details', function (Blueprint $table) {
            // Hapus kolom lama yang tidak diperlukan
            if (Schema::hasColumn('external_assignment_request_details', 'rundown_time')) {
                $table->dropColumn('rundown_time');
            }

            // Tambahkan kolom baru sesuai controller
            if (!Schema::hasColumn('external_assignment_request_details', 'start_time')) {
                $table->time('start_time')->nullable()->after('rundown_date');
            }

            if (!Schema::hasColumn('external_assignment_request_details', 'end_time')) {
                $table->time('end_time')->nullable()->after('start_time');
            }
        });
    }

    public function down(): void
    {
        Schema::table('external_assignment_request_details', function (Blueprint $table) {
            // Kembalikan seperti semula jika rollback
            if (Schema::hasColumn('external_assignment_request_details', 'start_time')) {
                $table->dropColumn('start_time');
            }

            if (Schema::hasColumn('external_assignment_request_details', 'end_time')) {
                $table->dropColumn('end_time');
            }

            if (!Schema::hasColumn('external_assignment_request_details', 'rundown_time')) {
                $table->time('rundown_time')->nullable()->after('rundown_date');
            }
        });
    }
}
