<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropUnusedColumnsFromTsCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn([
                '93536',
                '10',
                '1',
                '93537',
                '5',
                '30',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->integer('93536')->nullable();
            $table->integer('10')->nullable();
            $table->integer('1')->nullable();
            $table->integer('93537')->nullable();
            $table->integer('5')->nullable();
            $table->integer('30')->nullable();
        });
    }
}
