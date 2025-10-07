<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNotesToExternalAssignmentRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('external_assignment_requests', function (Blueprint $table) {
            $table->text('ear_hr_note')->nullable()->after('ear_hr_checked_at');
            $table->text('ear_finance_note')->nullable()->after('ear_finance_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('external_assignment_requests', function (Blueprint $table) {
            $table->dropColumn(['ear_hr_note', 'ear_finance_note']);
        });
    }
}
