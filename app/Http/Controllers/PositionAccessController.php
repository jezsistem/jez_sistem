<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\WebConfig;
use App\Models\User;
use App\Models\PositionAccess;

class PositionAccessController extends Controller
{
    protected function validateAccess($slug = null)
    {
        $segment = request()->segment(1);
        
        // Use provided slug or remove _v2 suffix for validation
        $slugToCheck = $slug ? $slug : str_replace('_v2', '', str_replace('-', '_', $segment));
        
        $validate = DB::table('user_menu_accesses')
            ->leftJoin('menu_accesses', 'menu_accesses.id', '=', 'user_menu_accesses.ma_id')->where([
                'u_id' => Auth::user()->id,
                'ma_slug' => $slugToCheck
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
            'subtitle' => DB::table('menu_accesses')->where('ma_slug', '=', 'position-access')->first()->ma_title,
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1),
        ];
        return view('app.position_access.position_access', compact('data'));
    }

    public function indexUpdated()
    {
        $this->validateAccess('position-access'); // Use original slug for access validation
        $user = new User;
        $select = ['*'];
        $where = [
            'users.id' => Auth::user()->id
        ];
        $user_data = $user->checkJoinData($select, $where)->first();
        $title = WebConfig::select('config_value')->where('config_name', 'app_title')->get()->first()->config_value;
        $data = [
            'title' => $title,
            'subtitle' => DB::table('menu_accesses')->where('ma_slug', '=', 'position-access')->first()->ma_title,
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1),
        ];
        return view('app.updated_position_access.position_access', compact('data'));
    }

    public function getDatatablesForSimple(Request $request)
    {
        try {
            $page = $request->get('page', 1);
            $perPage = $request->get('per_page', 25);
            $search = $request->get('search', '');

            $query = DB::table('position_access')
                ->leftJoin('user_positions', 'user_positions.id', '=', 'position_access.position_id')
                ->select(
                    'user_positions.up_name',
                    'position_access.id',
                    'position_access.position_id',
                    DB::raw('GROUP_CONCAT(DISTINCT ts_position_access.action ORDER BY ts_position_access.action SEPARATOR ";") as akses')
                )
                ->groupBy('user_positions.up_name', 'position_access.position_id');

            if ($search) {
                $query->where(function($w) use($search){
                    $w->orWhere('user_positions.up_name', 'LIKE', "%$search%")
                        ->orWhere('position_access.action', 'LIKE', "%$search%");
                });
            }

            // Get all data first to handle GROUP_CONCAT
            $allData = $query->orderBy('user_positions.up_name', 'asc')
                ->orderBy('position_access.id', 'desc')
                ->get();

            $total = $allData->count();
            $totalPages = ceil($total / $perPage);

            $data = $allData->skip(($page - 1) * $perPage)
                ->take($perPage)
                ->map(function ($row) {
                    $actions = explode(';', $row->akses);
                    $actionTypes = ['create', 'read', 'update', 'delete'];
                    $canUpdate = function_exists('hasAccess') && hasAccess(auth()->user()->up_id ?? 0, 'update');
                    $canDelete = function_exists('hasAccess') && hasAccess(auth()->user()->up_id ?? 0, 'delete');

                    $toggles = '';
                    foreach ($actionTypes as $actionType) {
                        $checked = in_array($actionType, $actions) ? 'checked' : '';
                        $disabled = !$canUpdate ? 'disabled' : '';
                        $toggles .= '<div class="inline-flex flex-col items-end mr-2">
                            <label class="text-xs mb-1">' . ucfirst($actionType) . '</label>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" class="sr-only peer switch-access" ' . $checked . ' ' . $disabled . ' 
                                       data-action="' . $actionType . '" 
                                       data-position-id="' . $row->position_id . '">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[\'\'] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>';
                    }

                    $deleteBtn = '';
                    if ($canDelete) {
                        $deleteBtn = '<button class="px-3 py-1 text-sm font-medium text-white bg-red-500 rounded-md hover:bg-red-700 delete-position-btn" data-position-id="' . $row->position_id . '">
                            <i class="fas fa-trash mr-1"></i>Hapus
                        </button>';
                    }

                    return [
                        'id' => $row->id,
                        'position_id' => $row->position_id,
                        'up_name' => $row->up_name ?? '-',
                        'akses' => $toggles,
                        'actions' => $deleteBtn
                    ];
                })
                ->values()
                ->all();

            $no = ($page - 1) * $perPage + 1;
            foreach ($data as &$row) {
                $row['DT_RowIndex'] = $no++;
            }

            return response()->json([
                'data' => $data,
                'total' => $total,
                'total_pages' => $totalPages,
                'current_page' => (int) $page,
                'per_page' => (int) $perPage
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load data: ' . $e->getMessage()], 500);
        }
    }

    public function storeData(Request $request)
    {
        $position_id = $request->position_id;
        $action = 'read';

        $data = [
            'position_id' => $position_id,
            'action' => $action,
        ];

        PositionAccess::create($data);

        return response()->json([
            'success' => 'Data berhasil disimpan.',
            'status' => 200
        ]);
    }

    public function reloadPosition()
    {
        $position = DB::table('user_positions')->where('up_is_active', '=', 1)->orderBy('up_level', 'desc')->get();
        return view('app.updated_position_access._positions', compact('position'));
    }

    public function deleteData(Request $request, $position_id)
    {
        PositionAccess::where('position_id', $position_id)->delete();
        $r['status'] = 200;
        $r['message'] = 'Data berhasil dihapus.';
        return response()->json($r);
    }

    public function changeAccess(Request $request)
    {
        $position_id = $request->position_id;
        $action = $request->action;
        $is_checked = $request->checked;

        if ($is_checked == 'true') {
            // Tambahkan akses jika belum ada
            $exists = PositionAccess::where('position_id', $position_id)
                ->where('action', $action)
                ->exists();

            if (!$exists) {
                PositionAccess::create([
                    'position_id' => $position_id,
                    'action' => $action,
                ]);
            }
        } else {
            // Hapus akses jika ada
            PositionAccess::where('position_id', $position_id)
                ->where('action', $action)
                ->delete();
        }

        $r['status'] = 200;
        $r['message'] = 'Akses berhasil diperbarui.';

        return response()->json($r);
    }
}
