<?php

use Illuminate\Support\Facades\DB;

if (!function_exists('hasAccess')) {
    function hasAccess($positionId, $permission)
    {
        return DB::table('position_access')
        ->where('position_id', $positionId)
        ->where('action', $permission)
        ->exists();
    }
}


//use Illuminate\Support\Facades\DB;
//
//if (!function_exists('hasAccess')) {
//    function hasAccess($positionId, $permission, $userId = null)
//    {
//        if (!$userId && auth()->check()) {
//            $userId = auth()->id();
//        }
//
//        $isAdmin = DB::table('user_groups')
//            ->where('user_id', $userId)
//            ->where('group_id', 1)
//            ->exists();
//
//        if ($isAdmin) {
//            return true;
//        }
//
//        return DB::table('position_access')
//            ->where('position_id', $positionId)
//            ->where('akses', $permission)
//            ->exists();
//    }
//}
