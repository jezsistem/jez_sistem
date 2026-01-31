<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLinktreeLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('linktree_links', function (Blueprint $table) {
            $table->id();

            $table->enum('type', [
                'whatsapp',
                'marketplace',
                'report',
                'maps'
            ]);

            $table->enum('platform', [
                'whatsapp',
                'tiktok',
                'shopee',
                'tokopedia',
                'gmaps'
            ])->nullable();

            $table->enum('area', [
                'malang',
                'surabaya',
                'kediri',
                'jember',
                'sidoarjo',
                'semarang'
            ])->nullable();
            $table->string('label');
            $table->string('url');

            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('linktree_links');
    }
}
