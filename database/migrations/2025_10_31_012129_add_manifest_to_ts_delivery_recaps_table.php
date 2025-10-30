<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddManifestToTsDeliveryRecapsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('delivery_recaps', function (Blueprint $table) {
            $table->string('manifest_number')->nullable()->after('expedition_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('delivery_recaps', function (Blueprint $table) {
            $table->dropColumn('manifest_number');
        });
    }
}
