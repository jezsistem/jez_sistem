<?php

namespace App\Http\Controllers;

use App\Models\MenuAccessTemplate;
use App\Models\MenuTitle;
use App\Models\User;
use App\Models\UserDivision;
use App\Models\WebConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MenuAccessTemplateController extends Controller
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
        $divisions = UserDivision::orderBy('ud_name')->get();
        return view('app.menu_access_template.menu_access_template', compact('data', 'divisions'));
    }

    public function datatable(){
        $query = MenuAccessTemplate::with('division')->orderBy('created_at', 'desc');
        return datatables($query)
        ->addIndexColumn()
        ->addColumn('actions', function($row){
            $buttons = '<a data-template_id="'.$row->id.'" class="btn btn-sm btn-warning mr-1" id="edit_template_btn">Edit</a>';
            $buttons .= '<button data-template_id="'.$row->id.'" class="btn btn-sm btn-danger btn-delete-template" id="delete_template_btn">Delete</button>';
            return $buttons;
        })
        ->rawColumns(['actions'])
        ->make(true);
    }

    public function getMenuAccesses(Request $request)
    {
        $menu_accesses = MenuTitle::with('menuAccesses')->orderBy('mt_sort')->get();

        return response()->json(['menu_accesses' => $menu_accesses]);
    }

    public function store(Request $request) {
        $template_name = $request->template_name;
        $division_id = $request->division_id;
        $description = $request->description;
        $menu_access_ids = $request->menu_access_ids;

        DB::beginTransaction();
        try {
            $template_id = MenuAccessTemplate::insertGetId([
                'template_name' => $template_name,
                'division_id' => $division_id,
                'description' => $description,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($menu_access_ids as $ma_id) {
                DB::table('menu_access_template_details')->insert([
                    'menu_access_template_id' => $template_id,
                    'menu_access_id' => $ma_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();
            return response()->json(['success' => 200, 'message' => 'Menu Access Template created successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => 500, 'message' => 'Failed to create Menu Access Template.', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy(MenuAccessTemplate $id){
        try {
            $id->delete();
            return response()->json(['success' => 200, 'message' => 'Menu Access Template deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => 500, 'message' => 'Failed to delete Menu Access Template.', 'error' => $e->getMessage()], 500);
        }
    }

    public function edit($id){
        $template = MenuAccessTemplate::with('menuAccessTemplateDetails')->find($id);
        $menu_access_ids = $template->menuAccessTemplateDetails->pluck('menu_access_id')->toArray();
        return response()->json([
            'status' => 200,
            'id' => $template->id,
            'template_name' => $template->template_name,
            'division_id' => $template->division_id,
            'description' => $template->description,
            'menu_access_ids' => $menu_access_ids,
        ]);
    }

    public function update($id, Request $request) {
        $template_name = $request->template_name;
        $division_id = $request->division_id;
        $description = $request->description;
        $menu_access_ids = $request->menu_access_ids;

        DB::beginTransaction();
        try {
            MenuAccessTemplate::where('id', $id)->update([
                'template_name' => $template_name,
                'division_id' => $division_id,
                'description' => $description,
                'updated_at' => now(),
            ]);

            DB::table('menu_access_template_details')->where('menu_access_template_id', $id)->delete();

            foreach ($menu_access_ids as $ma_id) {
                DB::table('menu_access_template_details')->insert([
                    'menu_access_template_id' => $id,
                    'menu_access_id' => $ma_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();
            return response()->json(['success' => 200, 'message' => 'Menu Access Template updated successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => 500, 'message' => 'Failed to update Menu Access Template.', 'error' => $e->getMessage()], 500);
        }
    }
}
