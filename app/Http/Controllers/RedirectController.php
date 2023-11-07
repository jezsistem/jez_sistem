<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\WebConfig;
use App\Models\User;
use App\Models\UserActivity;

class RedirectController extends Controller
{
    public function index() 
    {
        $menu = DB::table('user_menu_accesses')
        ->select('ma_slug')
        ->leftJoin('menu_accesses', 'menu_accesses.id', '=', 'user_menu_accesses.ma_id')->where([
            'u_id' => Auth::user()->id,
            'uma_default' => '1'
        ])->first();
        if (!empty($menu)) {
            return redirect()->to('/'.$menu->ma_slug);
        } else {
            dd('Anda belum memiliki default menu access, silahkan hubungi administrator untuk setting');
        }
    }
}
