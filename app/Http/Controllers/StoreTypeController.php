<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\WebConfig;
use App\Models\User;
use App\Models\StoreType;
use App\Models\UserActivity;

class StoreTypeController extends Controller
{
    public function getDatatables(Request $request)
    {
        if(request()->ajax()) {
            return datatables()->of(StoreType::select('id', 'stt_name', 'stt_description', 'stt_delete', 'created_at', 'updated_at')
            ->where('stt_delete', '!=', '1'))
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhere('stt_name', 'LIKE', "%$search%")
                        ->orWhere('stt_description', 'LIKE', "%$search%");
                    });
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function storeData(Request $request)
    {
        $store_type = new StoreType;
        $mode = $request->input('_mode_stt');
        $id = $request->input('_id_stt');

        $data = [
            'stt_name' => $request->input('stt_name'),
            'stt_description' => $request->input('stt_description'),
            'stt_delete' => '0',
        ];

        $store = $store_type->storeData($mode, $id, $data);
        if ($store) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        $item = [
            'item' => $request->input('stt_name'),
            'old_item' => $request->input('_old_item')
        ];
        $this->UserActivity($mode, $item);
        return json_encode($r);
    }

    public function deleteData(Request $request)
    {
        $store_type = new StoreType;
        $id = $request->input('_id');
        $store = $store_type->deleteData($id);
        if ($store) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        $this->UserActivity('delete', $request->input('_item'));
        return json_encode($r);
    }

    public function reloadStoreType()
    {
        $data = [
            'stt_id' => StoreType::where('stt_delete', '!=', '1')->orderByDesc('id')->pluck('stt_name', 'id'),
		];
        return view('app.store._reload_store_type', compact('data'));
    }

    protected function UserActivity($mode, $item)
    {
        $user_activity = new UserActivity;
        if ($mode == 'edit') {
            $activity = 'Mengubah data Store dari '.$item['old_item'].' menjadi '.$item['item'];
        } else if ( $mode == 'add' ) {
            $activity = 'Menambah data Store '.$item['item'];
        } else {
            $activity = 'Menghapus data Store '.$item;
        }
        $data = [
            'user_id' => Auth::user()->id,
            'ua_description' => $activity
        ];
        $user_activity->storeData($data);
    }
}
