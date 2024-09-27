<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AllstockController extends Controller
{

    protected function validateAccess()
    {
        $validate = DB::table('user_menu_accesses')
        ->leftJoin('menu_accesses', 'menu_accesses.id', '=', 'user_menu_accesses.ma_id')->where([
            'u_id' => Auth::user()->id,
            'ma_slug' => request()->segment(1)
        ])->exists();
        if (!$validate) {
            dd("Anda tidak memiliki akses ke menu ini, hubungi Administrator");
        }
    }

    protected function sidebar()
    {
        $ma_id = DB::table('user_menu_accesses')->select('ma_id')
        ->where('u_id', Auth::user()->id)->get();
        $ma_id_arr = array();
        if (!empty($ma_id)) {
            foreach ($ma_id as $row) {
                array_push($ma_id_arr, $row->ma_id);
            }
        }

        $sidebar = array();
        $mt = DB::table('menu_titles')->orderBy('mt_sort')->get();
        if (!empty($mt->first())) {
            foreach ($mt as $row) {
                $ma = DB::table('menu_accesses')
                ->where('mt_id', '=', $row->id)
                ->whereIn('id', $ma_id_arr)
                ->orderBy('ma_sort')->get();
                if (!empty($ma->first())) {
                    $row->ma = $ma;
                    array_push($sidebar, $row);
                }
            }
        }
        return $sidebar;
    }
    
    protected function UserActivity($activity)
    {
        UserActivity::create([
            'user_id' => Auth::user()->id,
            'ua_description' => $activity,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }
    public function index()
    {
        // Contoh data statis untuk stok barang
        $stocks = [
            ['name' => 'Produk A', 'quantity' => 10, 'price' => 50000],
            ['name' => 'Produk B', 'quantity' => 5, 'price' => 30000],
            ['name' => 'Produk C', 'quantity' => 20, 'price' => 15000],
        ];

        $user = new User();
        $select = ['*'];
        $where = [
            'users.id' => Auth::user()->id
        ];

        $user_data = $user->checkJoinData($select, $where)->first();

        $data = [
            'title' => 'Halaman Stok Produk',
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
        ];
        
        // Mengirim data ke view
        return view('app.allstock.allstock', ['stocks' => $stocks], compact('data'));
    }
}
