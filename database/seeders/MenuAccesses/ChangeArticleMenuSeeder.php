<?php

namespace Database\Seeders\MenuAccesses;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChangeArticleMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('menu_accesses')
            ->where('ma_title', 'like', '%Item Name%')
            ->update(['ma_title' => DB::raw("REPLACE(ma_title, 'Item Name', 'Artikel')")]);

        DB::table('menu_accesses')
            ->where('ma_title', 'like', '%Size%')
            ->update(['ma_title' => DB::raw("REPLACE(ma_title, 'Size', 'Variant')")]);
    }
}
