<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNoteAndProofImageToDeliveryRecapsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('delivery_recaps', function (Blueprint $table) {
            $table->text('note')->nullable()->after('import_file');
            $table->string('proof_image')->nullable()->after('note');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('delivery_recaps', function (Blueprint $table) {
            $table->dropColumn(['note', 'proof_image']);
        });
    }
}
