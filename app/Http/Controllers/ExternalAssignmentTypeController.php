<?php

namespace App\Http\Controllers;

use App\Exports\DataPerusahaanExport;
use App\Models\DataPerusahaan;
use App\Models\ExternalAssignmentType;
use App\Models\User;
use App\Models\WebConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ExternalAssignmentTypeController extends Controller
{
    protected function validateAccess($slug = null)
    {
        $segment = $slug ?? request()->segment(1);
        $slugToCheck = str_replace('_v2', '', $segment);

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
            'subtitle' => DB::table('menu_accesses')->where('ma_slug', '=', request()->segment(1))->first()->ma_title,
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1),
        ];
        return view('app.external_assignment_type.external_assignment_type', compact('data'));
    }

    public function getDatatables(Request $request)
    {
        if(request()->ajax()) {
            return datatables()->of(ExternalAssignmentType::select('id', 'ea_name', 'ea_desc'))
                ->filter(function ($instance) use ($request) {
                    if (!empty($request->get('search'))) {
                        $instance->where(function($w) use($request){
                            $search = $request->get('search');
                            $w->orWhere('ea_name', 'LIKE', "%$search%");
                        });
                    }
                })
                ->addIndexColumn()
                ->make(true);
        }
    }


    public function storeData(Request $request)
    {
        $ea_type = new ExternalAssignmentType();
        $mode = $request->input('_mode');
        $id = $request->input('_id');

        $data = [
            'ea_name' => ltrim($request->input('ea_name')),

            'ea_desc' => $request->input('ea_desc'),
        ];

        $save = $ea_type->storeData($mode, $id, $data);
        if ($save) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function deleteData(Request $request)
    {
        $ea_type = new ExternalAssignmentType();
        $id = $request->input('_id');
        $save = $ea_type->deleteData($id);
        if ($save) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function checkExistsExternalTypes(Request $request)
    {
        $check = ExternalAssignmentType::where(['ea_name' => strtoupper($request->_ea_name)])->exists();
        if ($check) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }


    
    public function exportData(Request $request)
    {
        try {
            $type = $request->get('type');

            $fileName = 'Export_Data_Perusahaan_' . date('Y-m-d') . '.xlsx';

            return Excel::download(new DataPerusahaanExport($type), $fileName);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * V2 - Updated version with Tailwind CSS
     */
    public function indexUpdated(Request $request)
    {
        $this->validateAccess();
        
        $title = 'External Assignment Types';
        $user = auth()->user();
        $user_data = DB::table('users')->where('id', $user->id)->first();
        
        $data = [
            'title' => $title,
            'subtitle' => DB::table('menu_accesses')->where('ma_slug', '=', 'external_assignment_type')->first()->ma_title,
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => 'external_assignment_type'
        ];

        return view('app.updated_external_assignment_type.index', compact('data'));
    }

    public function getDatatablesForSimple(Request $request)
    {
        $search = $request->get('search', '');
        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', 25);

        $query = DB::table('external_assignment_types')
            ->select('id', 'ea_name', 'ea_desc');

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('ea_name', 'LIKE', "%{$search}%")
                  ->orWhere('ea_desc', 'LIKE', "%{$search}%");
            });
        }

        $total = $query->count();
        $data = $query->orderBy('id', 'desc')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        return response()->json([
            'data' => $data,
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'last_page' => ceil($total / $perPage)
        ]);
    }

    public function storeForSimple(Request $request)
    {
        $mode = $request->input('_mode');
        $id = $request->input('_id');

        $data = [
            'ea_name' => ltrim($request->input('ea_name')),
            'ea_desc' => $request->input('ea_desc'),
        ];

        $ea_type = new ExternalAssignmentType();
        $save = $ea_type->storeData($mode, $id, $data);
        
        if ($save) {
            return response()->json(['success' => true, 'message' => 'Data berhasil disimpan']);
        } else {
            return response()->json(['success' => false, 'message' => 'Data tidak tersimpan'], 400);
        }
    }

    public function deleteForSimple(Request $request)
    {
        $id = $request->input('_id');
        $ea_type = new ExternalAssignmentType();
        $save = $ea_type->deleteData($id);
        
        if ($save) {
            return response()->json(['success' => true, 'message' => 'Data berhasil dihapus']);
        } else {
            return response()->json(['success' => false, 'message' => 'Gagal hapus data'], 400);
        }
    }

    public function checkExistsForSimple(Request $request)
    {
        $ea_name = $request->input('_ea_name');
        $check = ExternalAssignmentType::where('ea_name', strtoupper($ea_name))->exists();
        
        return response()->json(['exists' => $check]);
    }

    public function getForSimple($id)
    {
        $data = DB::table('external_assignment_types')
            ->where('id', $id)
            ->first();
        
        if ($data) {
            return response()->json(['success' => true, 'data' => $data]);
        } else {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
        }
    }
}
