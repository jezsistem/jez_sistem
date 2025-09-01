<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PositionAccessController extends Model
{
    use HasFactory;

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
        $user = new User;
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
        return view('app.position_access.position_access', compact('data'));
    }

    public function getDatatables(Request $request)
    {
        $search = $request->search;
        $query = DB::table('position_access')
            ->leftJoin('user_positions', 'user_positions.id', '=', 'position_access.position_id')
            ->select(
                'user_positions.up_name',
                'position_access.route',
                'position_access.id',
                'position_access.position_id',
                DB::raw('GROUP_CONCAT(DISTINCT ts_position_access.action ORDER BY ts_position_access.action SEPARATOR ";") as akses')
            )
            ->where(function ($q) use ($search) {
                $q->where('user_positions.up_name', 'like', '%' . $search . '%')
                    ->orWhere('position_access.route', 'like', '%' . $search . '%')
                    ->orWhere('position_access.action', 'like', '%' . $search . '%');
            })
            ->groupBy('position_access.route', 'user_positions.up_name')
            ->orderBy('user_positions.up_name', 'asc')
            ->orderBy('position_access.id', 'desc');

        return datatables()->of($query)
            ->addIndexColumn()
            ->editColumn('akses', function ($row) {
                $actions = explode(';', $row->akses);
                $toggles = '';

                $actionTypes = ['create', 'read', 'update', 'delete'];
                foreach ($actionTypes as $actionType) {
                    $checked = in_array($actionType, $actions) ? 'checked' : '';
                    $toggles .= '<div style="display: flex; flex-direction: column; align-items: flex-end; margin-right: 10px; display: inline-flex;">
            <label style="margin-bottom: 5px;">' . ucfirst($actionType) . '</label>
            <label class="switch">
            <input id="switch_access" type="checkbox" ' . $checked . ' data-action="' . $actionType . '" data-position-id="' . $row->position_id . '" data-route="' . $row->route . '">
            <span class="slider round"></span>
            </label>
            </div>';
                }

                return $toggles;
            })
            ->addColumn('actions', function ($row) {
                $updateBtn = '<button class="btn btn-warning btn-sm" onclick="editData(' . $row->route . ')" title="Edit">
                <i class="fas fa-edit"></i>
            </button>';
                $deleteBtn = '<button class="btn btn-danger btn-sm" data-route="' . $row->route . '" data-position-id="' . $row->position_id . '" title="Delete" id="deleteBtn">
                <i class="fas fa-trash"></i>
            </button>';
                return  $deleteBtn;
            })
            ->rawColumns(['akses', 'actions'])
            ->toJson();
    }

    public function storeData(Request $request)
    {
        $position_id = $request->position_id;
        $route = $request->route;
        $action = 'read';

        $data = [
            'position_id' => $position_id,
            'route' => $route,
            'action' => $action,
        ];

        PositionAccess::create($data);

        return response()->json(['success' => 'Data berhasil disimpan.']);
    }

    public function reloadPosition()
    {
        $position = DB::table('user_positions')->where('up_is_active', '=', 1)->orderBy('up_level', 'desc')->get();
        return view('app.position_access._positions', compact('position'));
    }

    public function deleteData($route, $position_id)
    {
        PositionAccess::where('route', $route)->where('position_id', $position_id)->delete();
        $r['status'] = 200;
        $r['message'] = 'Data berhasil dihapus.';
        return response()->json($r);
    }

    public function changeAccess(Request $request)
    {
        $position_id = $request->position_id;
        $route = $request->route;
        $action = $request->action;
        $is_checked = $request->checked;

        if ($is_checked == 'true') {
            // Tambahkan akses jika belum ada
            $exists = PositionAccess::where('position_id', $position_id)
                ->where('route', $route)
                ->where('action', $action)
                ->exists();

            if (!$exists) {
                PositionAccess::create([
                    'position_id' => $position_id,
                    'route' => $route,
                    'action' => $action,
                ]);
            }
        } else {
            // Hapus akses jika ada
            PositionAccess::where('position_id', $position_id)
                ->where('route', $route)
                ->where('action', $action)
                ->delete();
        }

        $r['status'] = 200;
        $r['message'] = 'Akses berhasil diperbarui.';

        return response()->json($r);
        
    }
}
