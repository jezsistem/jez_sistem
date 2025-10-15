<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLocationAndPhotoToAttendanceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('attendance', function (Blueprint $table) {
            $table->string('at_location_in')->nullable()->after('at_time_out');
            $table->string('at_location_out')->nullable()->after('at_location_in');
            $table->string('at_photos_in')->nullable()->after('at_location_out');
            $table->string('at_photos_out')->nullable()->after('at_photos_in');
            $table->text('at_address_attendance')->nullable()->after('at_photos_out');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance', function (Blueprint $table) {
            $table->dropColumn([
                'at_location_in',
                'at_location_out',
                'at_photos_in',
                'at_photos_out',
                'at_address_attendance',
            ]);
        });
    }
}
