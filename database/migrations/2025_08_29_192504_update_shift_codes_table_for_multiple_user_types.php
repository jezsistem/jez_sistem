<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdateShiftCodesTableForMultipleUserTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // First, create the pivot table if it doesn't exist
        if (!Schema::hasTable('shift_code_user_types')) {
            Schema::create('shift_code_user_types', function (Blueprint $table) {
                $table->id();
                $table->foreignId('shift_code_id')->constrained('shift_codes')->onDelete('cascade');
                $table->foreignId('user_type_id')->constrained('user_types')->onDelete('cascade');
                $table->timestamps();
                
                $table->unique(['shift_code_id', 'user_type_id']);
                $table->index('shift_code_id');
                $table->index('user_type_id');
            });
        }

        // Migrate existing data from sc_type to pivot table
        $shiftCodes = DB::table('shift_codes')->get();
        $userTypes = DB::table('user_types')->get()->keyBy('ut_name');
        
        foreach ($shiftCodes as $shiftCode) {
            if ($shiftCode->sc_type && $shiftCode->sc_type !== 'ALL') {
                // Find user type by name
                $userType = $userTypes->get($shiftCode->sc_type);
                if ($userType) {
                    DB::table('shift_code_user_types')->insert([
                        'shift_code_id' => $shiftCode->id,
                        'user_type_id' => $userType->id,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            } else if ($shiftCode->sc_type === 'ALL') {
                // If sc_type is 'ALL', add all user types
                foreach ($userTypes as $userType) {
                    DB::table('shift_code_user_types')->insert([
                        'shift_code_id' => $shiftCode->id,
                        'user_type_id' => $userType->id,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }
        }

        // After migration, we can optionally remove the sc_type column
        // But let's keep it for now to ensure backward compatibility
        // Schema::table('shift_codes', function (Blueprint $table) {
        //     $table->dropColumn('sc_type');
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Remove pivot table
        Schema::dropIfExists('shift_code_user_types');
        
        // Note: We don't restore sc_type column as it might cause data loss
        // If you need to rollback, you'll need to manually restore the data
    }
}
