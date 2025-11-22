<?php

namespace App\Http\Controllers;

use App\Models\ModalLock;
use App\Models\User;
use App\Models\WebConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ModalLockController extends Controller
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

    public function index()
    {
        $this->validateAccess();
        $user = new User();
        $select = ['*'];
        $where = [
            'users.id' => Auth::user()->id
        ];
        $user_data = $user->checkJoinData($select, $where)->first();
        $title = WebConfig::select('config_value')->where('config_name', 'app_title')->get()->first()->config_value;
        $data = [
            'title' => $title,
            'subtitle' => DB::table('menu_accesses')->where('ma_slug', '=', request()->segment(1))->first()->ma_title,
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1),
        ];
        return view('app.modal_lock.modal_lock', compact('data'));
    }

    public function getDatatables(Request $request)
    {
        if (request()->ajax()) {
            return datatables()->of(ModalLock::select('modal_locks.id', 'u_name', 'lockable_id', 'lockable_type', 'identifier', 'expires_at')->join('users', 'users.id', '=', 'modal_locks.user_id')->orderBy('modal_locks.id', 'desc'))
                ->filter(function ($instance) use ($request) {
                    if (!empty($request->get('search'))) {
                        $instance->where(function ($w) use ($request) {
                            $search = $request->get('search');
                            $w->orWhere('u_name', 'LIKE', "%$search%")
                              ->orWhere('lockable_id', 'LIKE', "%$search%")
                              ->orWhere('lockable_type', 'LIKE', "%$search%")
                              ->orWhere('identifier', 'LIKE', "%$search%")
                              ->orWhere('expires_at', 'LIKE', "%$search%");
                        });
                    }
                })
                ->editColumn('expires_at', function ($data) {
                    return \Carbon\Carbon::parse($data->expires_at)->locale('id')->isoFormat('DD MMMM YYYY HH:mm:ss');
                })
                ->addColumn('action', function ($data) {
                    $btn = '<button class="btn btn-sm btn-danger delete-btn" id="delete_modal_lock_btn" data-id="' . $data->id . '" onclick="deleteLockModal(' . $data->id . ')"><i class="fa fa-trash"></i> Delete</button>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function deleteData(Request $request)
    {
        $id = $request->input('id');
        $modalLock = ModalLock::find($id);
        if ($modalLock) {
            $modalLock->delete();
            return response()->json(['status' => '200', 'message' => 'Modal lock entry deleted successfully.']);
        } else {
            return response()->json(['status' => '400', 'message' => 'Modal lock entry not found.'], 404);
        }
    }
}
