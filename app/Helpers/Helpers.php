<?php

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

if (!function_exists('validateAccess')) {
  function validateAccess()
  {
    $validate = DB::table('user_menu_accesses')
      ->leftJoin('menu_accesses', 'menu_accesses.id', '=', 'user_menu_accesses.ma_id')->where([
          'u_id' => Auth::user()->id,
          'ma_slug' => request()->segment(1)
        ])->exists();
        dd();
    if (!$validate) {
      abort(403, "You do not have access to this menu. Contact the Administrator.");
    }
  }
}

if (!function_exists('sidebar')) {
  function sidebar()
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
}

if (!function_exists('sCrypt')) {
  function sCrypt($str)
  {
    return Crypt::encryptString($str);
  }
}

if (!function_exists('sDecrypt')) {
  function sDecrypt($str)
  {
    return Crypt::decryptString($str);
  }
}
