<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusAndApprovalToExternalAssignmentsRequestsTable extends Migration
{
    public function up()
    {
        Schema::table('external_assignment_requests', function (Blueprint $table) {
            // request_by setelah ea_id
            $table->unsignedBigInteger('request_by')->after('ea_id');

            // status dan approval flow setelah ear_cash_advance
            $table->enum('ear_status', [
                'Pending Approval',
                'Approved',
                'Rejected',
                'Reporting',
                'HR Check',
                'Finance Process',
                'DONE'
            ])->default('Pending Approval')->after('ear_cash_advance');

            $table->unsignedBigInteger('ear_approved_by')->nullable()->after('ear_status');
            $table->timestamp('ear_approved_at')->nullable()->after('ear_approved_by');

            $table->unsignedBigInteger('ear_hr_checked_by')->nullable()->after('ear_approved_at');
            $table->timestamp('ear_hr_checked_at')->nullable()->after('ear_hr_checked_by');

            $table->unsignedBigInteger('ear_finance_by')->nullable()->after('ear_hr_checked_at');
            $table->timestamp('ear_finance_at')->nullable()->after('ear_finance_by');

            // foreign keys
            $table->foreign('request_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('ear_approved_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('ear_hr_checked_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('ear_finance_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('external_assignment_requests', function (Blueprint $table) {
            $table->dropForeign(['request_by']);
            $table->dropForeign(['ear_approved_by']);
            $table->dropForeign(['ear_hr_checked_by']);
            $table->dropForeign(['ear_finance_by']);

            $table->dropColumn([
                'request_by',
                'ear_status',
                'ear_approved_by',
                'ear_approved_at',
                'ear_hr_checked_by',
                'ear_hr_checked_at',
                'ear_finance_by',
                'ear_finance_at',
            ]);
        });
    }

}
