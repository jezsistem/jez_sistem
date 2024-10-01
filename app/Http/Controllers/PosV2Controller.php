<?php

namespace App\Http\Controllers;


use App\Models\User;
use App\Models\UserActivity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\WebConfig;


class PosV2Controller extends Controller
{


    
    public function index()
    {
        // Contoh data statis untuk stok barang
       
        
        $user = new User;
        $user = new User();
        $select = ['*'];
        $where = [
            'users.id' => Auth::user()->id
        ];

        $user_data = $user->checkJoinData($select, $where)->first();
        $select_activity = ['user_activities.id as uaid', 'u_name', 'ua_description', 'user_activities.created_at as ua_created_at'];
        $title = WebConfig::select('config_value')->where('config_name', 'app_title')->get()->first()->config_value;

        $data = [
        ];
        
        // Mengarahkan ke view yang ingin ditampilkan
        return view('app.posv2.dashboard-posv2');

    }
}
