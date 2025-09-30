<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BreakTimeMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get HR menu title
        $hrMenuTitle = DB::table('menu_titles')->where('mt_title', 'Human Resource')->first();

        if (!$hrMenuTitle) {
            echo "HR menu title not found. Please run HRMenuSeeder first.\n";
            return;
        }

        // Insert Break Time menu
        $breakTimeMenuId = DB::table('menu_accesses')->insertGetId([
            'mt_id' => $hrMenuTitle->id,
            'ma_title' => 'Break Time',
            'ma_slug' => 'break-times',
            'ma_sort' => 4,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        // Get all users
        $users = DB::table('users')->where('u_delete', '0')->get();

        // Assign menu access to all users
        foreach ($users as $user) {
            DB::table('user_menu_accesses')->insert([
                'u_id' => $user->id,
                'ma_id' => $breakTimeMenuId,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        echo "Break Time menu created and assigned to " . count($users) . " users.\n";
    }
} 