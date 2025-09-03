<?php

//use Illuminate\Support\Facades\DB;
//
//if (!function_exists('hasAccess')) {
//    function hasAccess($positionId, $permission)
//    {
//        return DB::table('position_access')
//        ->where('position_id', $positionId)
//        ->where('action', $permission)
//        ->exists();
//    }
//}


use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\UserDivision;
use App\Models\UserGroup;
use App\Models\PositionAccess;

if (! function_exists('hasAccess')) {
    function hasAccess($feature, $action)
    {
        $user = Auth::user();

        if (! $user) {
            return false;
        }

        $isAdminGroup = UserGroup::where('user_id', $user->id)
            ->where('group_id', 1)
            ->exists();

        $isAdminDivision = $user->division && $user->division->ud_code === 'HUMANRESOU';

        if ($isAdminGroup || $isAdminDivision) {
            return true;
        }

        return PositionAccess::where('position_id', $user->position_id)
            ->where('feature', $feature)
            ->where('akses', $action)
            ->exists();
    }
}

