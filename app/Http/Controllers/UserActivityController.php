<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\WebConfig;
use App\Models\User;
use App\Models\UserActivity;

class UserActivityController extends Controller
{
    public function getDatatables(Request $request)
    {
        if(request()->ajax()) {
            return datatables()->of(UserActivity::select('user_activities.id as uaid', 'st_name', 'u_name', 'ua_description', 'user_activities.created_at as ua_created_at')
            ->leftJoin('users', 'users.id', '=', 'user_activities.user_id')
            ->leftJoin('stores', 'stores.id', '=', 'users.st_id')
            ->orderByDesc('uaid'))
            ->editColumn('ua_created_at_show', function($data){ 
                return date('d-m-Y H:i:s', strtotime($data->ua_created_at));
            })
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhere('u_name', 'LIKE', "%$search%")
                        ->orWhere('ua_description', 'LIKE', "%$search%")
                        ->orWhere('st_name', 'LIKE', "%$search%");
                    });
                }
                if (!empty($request->get('st_id'))) {
                    $instance->where(function($w) use($request){
                        $st_id = $request->get('st_id');
                        if (!empty($st_id)) {
                            $w->whereIn('users.st_id', $st_id);
                        }
                    });
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }
}
