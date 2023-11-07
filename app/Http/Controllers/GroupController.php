<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\WebConfig;
use App\Models\User;
use App\Models\Group;

class GroupController extends Controller
{
    public function getDatatables(Request $request)
    {
        if(request()->ajax()) {
            return datatables()->of(Group::select('id', 'g_name', 'g_description', 'g_delete', 'created_at', 'updated_at')
            ->where('g_delete', '!=', '1'))
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhere('g_name', 'LIKE', "%$search%")
                        ->orWhere('g_description', 'LIKE', "%$search%");
                    });
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function storeData(Request $request)
    {
        $group = new Group;
        $mode = $request->input('_mode_gr');
        $id = $request->input('_id_gr');

        $data = [
            'g_name' => strtolower($request->input('gr_name')),
            'g_description' => $request->input('gr_description'),
            'g_delete' => '0',
        ];

        $store = $group->storeData($mode, $id, $data);
        if ($store) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function deleteData(Request $request)
    {
        $group = new Group;
        $id = $request->input('_id');
        $store = $group->deleteData($id);
        if ($store) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function reloadGroup()
    {
        $data = [
            'gr_id' => Group::where('g_delete', '!=', '1')->orderByDesc('id')->pluck('g_name', 'id'),
		];
        return view('app.user._reload_group', compact('data'));
    }
}
