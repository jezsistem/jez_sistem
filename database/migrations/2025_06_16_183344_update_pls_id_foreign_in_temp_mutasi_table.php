<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePlsIdForeignInTempMutasiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('temp_mutasi', function (Blueprint $table) {
            // Drop the existing foreign key constraint if it exists
            $table->dropForeign(['pls_id']);

            // Add the new foreign key constraint with ON DELETE CASCADE
            $table->foreign('pls_id')
                ->references('id')
                ->on('product_location_setups')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('temp_mutasi', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['pls_id']);

            // Re-add the foreign key constraint without ON DELETE CASCADE
            $table->foreign('pls_id')
                ->references('id')
                ->on('product_locations');
        });
    }
}
