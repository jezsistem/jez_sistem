<?php

namespace App\Http\Controllers;

use App\Models\ProductLocation;
use App\Models\StorageArea;
use App\Models\Store;
use App\Models\User;
use App\Models\UserActivity;
use App\Models\WebConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StorageAreaController extends Controller
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
            'segment' => request()->segment(1),
            'st_id' => Store::selectRaw('ts_stores.id as sid, CONCAT(st_name) as store')
                ->where('st_delete', '!=', '1')
                ->orderByDesc('sid')->pluck('store', 'sid'),
        ];
        return view('app.storage_area.storage_area', compact('data'));
    }

    public function show($id)
    {
        $storageArea = StorageArea::findOrFail($id);
        
        return response()->json([
            'data' => $storageArea,
            'message' => 'Storage area retrieved successfully.',
            'status' => true
        ]);
    }

    public function updateData(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:storage_areas,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $storageArea = StorageArea::findOrFail($request->input('id'));
            $storageArea->update([
                'name' => $request->input('name'),
                'description' => $request->input('description')
            ]);

            $this->UserActivity('Mengupdate Storage Area: ' . $storageArea->name);

            DB::commit();

            return response()->json([
                'message' => 'Storage area updated successfully.',
                'status' => true
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'message' => 'Failed to update storage area: ' . $e->getMessage(),
                'status' => false
            ], 500);
        }
    }

    public function deleteData($id)
    {
        try {
            DB::beginTransaction();

            $storageArea = StorageArea::findOrFail($id);
            
            // Check if there are any bins linked to this storage area
            $linkedBins = ProductLocation::where('sa_id', $storageArea->id)->count();
            
            if ($linkedBins > 0) {
                return response()->json([
                    'message' => 'Cannot delete storage area. There are bins linked to this area.',
                    'status' => false
                ], 400);
            }

            $storageAreaName = $storageArea->name;
            $storageArea->delete();

            $this->UserActivity('Menghapus Storage Area: ' . $storageAreaName);

            DB::commit();

            return response()->json([
                'message' => 'Storage area deleted successfully.',
                'status' => true
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'message' => 'Failed to delete storage area: ' . $e->getMessage(),
                'status' => false
            ], 500);
        }
    }

    public function storageAreaDatatables()
    {
        if (request()->ajax()) {
            $search = request()->input('search.value');
            if (request()->input('st_id')) {
                $st_id = request()->input('st_id');
            } else {
                $st_id = -1;
            }

            $query = DB::table('storage_areas')
                ->leftJoin('stores', 'storage_areas.st_id', '=', 'stores.id')
                ->select('storage_areas.*', 'stores.st_name as store_name')
                ->where('storage_areas.name', 'like', '%' . $search . '%');

            if ($st_id) {
                $query->where('storage_areas.st_id', '=', $st_id);
            }

            $data = $query->orderBy('storage_areas.created_at', 'desc')->get();

            return datatables()->of($data)
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function storageAreaList(Request $request)
    {
        $search = $request->input('search');

        if ($request->input('st_id')) {
            $st_id = $request->input('st_id');
        } else {
            $st_id = -1;
        }


        $query = DB::table('storage_areas')
            ->leftJoin('stores', 'storage_areas.st_id', '=', 'stores.id')
            ->select('storage_areas.*', 'stores.st_name as store_name');

        if ($search) {
            $query->where('storage_areas.name', 'like', '%' . $search . '%');
        }

        if ($st_id) {
            $query->where('storage_areas.st_id', '=', $st_id);
        }

        $storageAreas = $query->orderBy('storage_areas.created_at', 'desc')->get();

        return response()->json([
            'data' => $storageAreas,
            'message' => 'Storage areas retrieved successfully.',
            'status' => true
        ]);
    }

    public function binListNoArea(Request $request)
    {
        $search = $request->input('search');
        $st_id = $request->input('st_id');

        $query = ProductLocation::select('id', 'pl_code', 'pl_name', 'sa_id')
            ->where('pl_delete', '!=', '1')
            ->where('st_id', $st_id)
            ->where('sa_id', null);

        if ($search) {
            $query->where('pl_code', 'like', '%' . $search . '%');
        }

        $bins = $query->get()->groupBy('pl_name')->map(function ($group) {
            return [
                'pl_name' => $group->first()->pl_name,
                'bins' => $group->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'pl_code' => $item->pl_code,
                        'sa_id' => $item->sa_id,
                    ];
                })->values()
            ];
        })->values();

        return response()->json([
            'data' => $bins,
            'message' => 'Bins retrieved successfully.',
            'status' => true
        ]);
    }

    public function binList(Request $request)
    {
        $search = $request->input('search');
        $st_id = $request->input('st_id');
        $sa_id = $request->input('area_id');

        $query = ProductLocation::select('id', 'pl_code', 'pl_name', 'sa_id')
            ->where('pl_delete', '!=', '1')
            ->where('st_id', $st_id)
            ->where('sa_id', $sa_id);

        if ($search) {
            $query->where('pl_code', 'like', '%' . $search . '%');
        }

        $bins = $query->get()->groupBy('pl_name')->map(function ($group) {
            return [
                'pl_name' => $group->first()->pl_name,
                'bins' => $group->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'pl_code' => $item->pl_code,
                        'sa_id' => $item->sa_id,
                    ];
                })->values()
            ];
        })->values();

        return response()->json([
            'data' => $bins,
            'message' => 'Bins retrieved successfully.',
            'status' => true
        ]);
    }

    public function createData(Request $request)
    {
        $request->validate([
            'st_id' => 'required|exists:stores,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        $storageArea = new StorageArea();
        $storageArea->st_id = $request->input('st_id');
        $storageArea->name = $request->input('name');
        $storageArea->description = $request->input('description');
        $storageArea->save();

        $this->UserActivity('Menambahkan Storage Area: ' . $storageArea->name);

        return response()->json([
            'message' => 'Storage area created successfully.',
            'status' => true
        ]);
    }

    public function linkBinToStorageArea(Request $request)
    {
        $request->validate([
            'sa_id' => 'required|exists:storage_areas,id',
            'tempBinIds' => 'required|array',
            'tempBinIds.*' => 'required|exists:product_locations,id',
            'st_id' => 'required|exists:stores,id',
        ]);

        try {
            DB::beginTransaction();

            $saId = $request->input('sa_id');
            $tempBinIds = $request->input('tempBinIds');

            $updatedBins = [];
            foreach ($tempBinIds as $binId) {
                $productLocation = ProductLocation::find($binId);
                if ($productLocation) {
                    $productLocation->update(['sa_id' => $saId]);
                    $updatedBins[] = $productLocation->pl_code;
                }
            }

            if (!empty($updatedBins)) {
                $this->UserActivity('Mengaitkan Bin dengan Storage Area: ' . implode(', ', $updatedBins));

                DB::commit();

                return response()->json([
                    'message' => 'Bins linked to storage area successfully.',
                    'status' => true
                ]);
            }

            DB::rollback();
            return response()->json([
                'message' => 'No bins were updated.',
                'status' => false
            ], 400);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'message' => 'Failed to link bins to storage area: ' . $e->getMessage(),
                'status' => false
            ], 500);
        }
    }

    public function unlinkBinToStorageArea(Request $request)
    {
        $request->validate([
            'sa_id' => 'required|exists:storage_areas,id',
            'binIds' => 'required|array',
            'binIds.*' => 'required|exists:product_locations,id',
            'st_id' => 'required|exists:stores,id',
        ]);

        try {
            DB::beginTransaction();

            $binIds = $request->input('binIds');

            $updatedBins = [];
            foreach ($binIds as $binId) {
                $productLocation = ProductLocation::find($binId);
                if ($productLocation) {
                    $productLocation->update(['sa_id' => null]);
                    $updatedBins[] = $productLocation->pl_code;
                }
            }

            if (!empty($updatedBins)) {
                $this->UserActivity('Menghapus kaitan Bin dengan Storage Area: ' . implode(', ', $updatedBins));

                DB::commit();

                return response()->json([
                    'message' => 'Bins unlinked from storage area successfully.',
                    'status' => true
                ]);
            }

            DB::rollback();
            return response()->json([
                'message' => 'No bins were updated.',
                'status' => false
            ], 400);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'message' => 'Failed to unlink bins from storage area: ' . $e->getMessage(),
                'status' => false
            ], 500);
        }
    }
}
