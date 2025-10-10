<?php

namespace App\Http\Controllers;

use App\Models\UserActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\WebConfig;

class WarehouseIndexController extends Controller
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

    protected function UserActivity($activity)
    {
        UserActivity::create([
            'user_id' => Auth::user()->id,
            'ua_description' => $activity,
            'created_at' => date('Y-m-d H:i:s')
        ]);
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
            'stores' => DB::table('stores')->where('st_delete', '0')->orderBy('st_name')->get(),
            'segment' => request()->segment(1),
        ];
        return view('app.warehouse_index.warehouse_index', compact('data'));
    }

    public function getDatatables(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::table('warehouse_index')
                ->leftJoin('stores', 'stores.id', '=', 'warehouse_index.st_id')
                ->select('warehouse_index.*', 'stores.st_name')
                ->orderBy('warehouse_index.id', 'desc')
                ->get();
            return datatables()->of($data)
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function storeData(Request $request)
    {
        $request->validate([
            'st_id' => 'required',
            'w_code' => 'required',
        ]);

        DB::beginTransaction();
        try {

            if ($request->_mode == 'edit') {
                DB::table('warehouse_index')->where('id', $request->_id)->update([
                    'st_id' => $request->st_id,
                    'w_code' => $request->w_code,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            } else {
                DB::table('warehouse_index')->insert([
                    'st_id' => $request->st_id,
                    'w_code' => $request->w_code,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
            DB::commit();
            $this->UserActivity("Menambah data warehouse index");
            return response()->json(['status' => 'success', 'message' => 'Data berhasil disimpan.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage()]);
        }
    }

    public function deleteData(Request $request)
    {
        DB::beginTransaction();
        try {
            DB::table('warehouse_index')->where('id', $request->_id)->delete();
            DB::commit();
            $this->UserActivity("Menghapus data warehouse index");
            return response()->json(['status' => 'success', 'message' => 'Data berhasil dihapus.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage()]);
        }
    }
}
