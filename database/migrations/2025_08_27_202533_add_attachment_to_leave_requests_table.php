<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAttachmentToLeaveRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->string('lr_attachment_path')->nullable()->after('lr_reason');
            $table->string('lr_attachment_name')->nullable()->after('lr_attachment_path');
            $table->string('lr_attachment_type')->nullable()->after('lr_attachment_name');
            $table->integer('lr_attachment_size')->nullable()->after('lr_attachment_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->dropColumn(['lr_attachment_path', 'lr_attachment_name', 'lr_attachment_type', 'lr_attachment_size']);
        });
    }
}
