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

    public function getDatatablesForSimple(Request $request)
    {
        try {
            $page = $request->get('page', 1);
            $perPage = $request->get('per_page', 25);
            $search = $request->get('search', '');

            $query = Group::select('id', 'g_name', 'g_description', 'g_delete', 'created_at', 'updated_at')
                ->where('g_delete', '!=', '1');

            if ($search) {
                $query->where(function($w) use($search){
                    $w->orWhere('g_name', 'LIKE', "%$search%")
                        ->orWhere('g_description', 'LIKE', "%$search%");
                });
            }

            // Get all data first to handle pagination
            $allData = $query->orderByDesc('id')->get();

            $total = $allData->count();
            $totalPages = ceil($total / $perPage);

            $data = $allData->skip(($page - 1) * $perPage)
                ->take($perPage)
                ->map(function ($row) {
                    return [
                        'id' => $row->id,
                        'g_name' => $row->g_name ?? '-',
                        'g_description' => $row->g_description ?? '-',
                    ];
                })
                ->values()
                ->all();

            $no = ($page - 1) * $perPage + 1;
            foreach ($data as &$row) {
                $row['no'] = $no++;
            }

            return response()->json([
                'data' => $data,
                'total' => $total,
                'total_pages' => $totalPages,
                'current_page' => (int) $page,
                'per_page' => (int) $perPage
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Terjadi kesalahan saat memuat data: ' . $e->getMessage(),
                'data' => [],
                'total' => 0,
                'total_pages' => 0,
                'current_page' => 1,
                'per_page' => 25
            ], 500);
        }
    }
}
