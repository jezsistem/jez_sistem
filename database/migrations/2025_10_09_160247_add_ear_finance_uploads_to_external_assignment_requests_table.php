<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEarFinanceUploadsToExternalAssignmentRequestsTable extends Migration
{
    public function up(): void
    {
        Schema::table('external_assignment_requests', function (Blueprint $table) {
            $table->string('ear_finance_uploads')->nullable()->after('ear_finance_note');
        });
    }

    public function down(): void
    {
        Schema::table('external_assignment_requests', function (Blueprint $table) {
            $table->dropColumn('ear_finance_uploads');
        });
    }
}
