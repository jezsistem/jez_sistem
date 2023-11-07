<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\WebConfig;
use App\Models\User;
use App\Models\Topdeal;
use App\Models\TopdealDetail;
use App\Models\Product;

class TopdealController extends Controller
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
        return view('app.topdeal.topdeal', compact('data'));
    }

    public function getDatatables(Request $request)
    {
        if(request()->ajax()) {
            return datatables()->of(Topdeal::selectRaw('ts_topdeals.id as id, td_name, td_due_date, td_status, count(ts_top_deal_details.td_id) as article')
            ->leftJoin('top_deal_details', 'top_deal_details.td_id', '=', 'topdeals.id')
            ->groupBy('topdeals.id'))
            ->editColumn('td_due_date_show', function($data){
                return date('d/m/Y H:i:s', strtotime($data->td_due_date));
            })
            ->editColumn('td_due_date_val', function($data){
                $exp = explode(' ', $data->td_due_date);
                return $exp[0];
            })
            ->editColumn('td_due_time', function($data){
                $exp = explode(' ', $data->td_due_date);
                return $exp[1];
            })
            ->editColumn('td_status_show', function($data){
                if ($data->td_status == '1') {
                  $status = "<a class='btn-sm btn-success'>Aktif</a>";
                } else {
                  $status = "<a class='btn-sm btn-danger'>Nonaktif</a>";
                }
                return $status;
            })
            ->editColumn('article_show', function($data){
                return "<a class='btn-sm btn-primary' data-id='".$data->id."' id='article_detail_btn'>".$data->article."</a>";
            })
            ->rawColumns(['td_due_date_show', 'td_status_show', 'article_show'])
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhere('td_name', 'LIKE', "%$search%");
                    });
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function getTopdealsArticleDatatables(Request $request)
    {
        if(request()->ajax()) {
            return datatables()->of(TopdealDetail::select('top_deal_details.id as id', 'br_name', 'p_name', 'p_color')
            ->leftJoin('products', 'products.id', '=', 'top_deal_details.p_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
            ->where('top_deal_details.td_id', '=', $request->get('td_id')))
            ->editColumn('action', function($data){
                return "<a data-id='".$data->id."' id='delete_topdeals_article_btn' class='btn-sm btn-danger'>Hapus</a>";
            })
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function getArticleDatatables(Request $request)
    {
        if(request()->ajax()) {
            return datatables()->of(Product::select('products.id as id', 'br_name', 'p_name', 'p_color')
            ->leftJoin('brands', 'brands.id', '=', 'products.br_id'))
            ->editColumn('action', function($data){
                return "<a class='btn-sm btn-primary' data-id='".$data->id."' id='check_article_btn'>add</a>";
            })
            ->rawColumns(['action'])
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhereRaw('CONCAT(br_name," ", p_name," ", p_color) LIKE ?', "%$search%");
                    });
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function storeData(Request $request)
    {
        $topdeal = new Topdeal;
        $mode = $request->input('_mode');
        $id = $request->input('_id');

        $data = [
            'td_name' => $request->input('td_name'),
            'td_due_date' => $request->input('td_due_date').' '.$request->input('td_due_time'),
            'td_status' => $request->input('td_status'),
        ];

        $save = $topdeal->storeData($mode, $id, $data);
        if ($save) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function deleteData(Request $request)
    {
        $id = $request->input('_id');
        try {
            DB::table('top_deal_details')->where('td_id', $id)->delete();
            $delete = DB::table('topdeals')->where('id', $id)->delete();
            if ($delete) {
                $r['status'] = '200';
            } else {
                $r['status'] = '400';
            }
        } catch (\Illuminate\Database\QueryException $ex) {
            if($ex->getCode() === '23000') {
                $r['status'] = '400';
            }
        }
        return json_encode($r);
    }

    public function addTopdeals(Request $request)
    {
        $p_id = $request->post('p_id');
        $td_id = $request->post('td_id');
        $exists = TopdealDetail::where([
          'td_id' => $td_id,
          'p_id' => $p_id
        ])->exists();
        if ($exists) {
          $r['status'] = '300';
        } else {
          $insert = TopdealDetail::insert([
            'td_id' => $td_id,
            'p_id' => $p_id
          ]);
          if (!empty($insert)) {
            $r['status'] = '200';
          } else {
            $r['status'] = '400';
          }
        }
        return json_encode($r);
    }

    public function deleteTopdeals(Request $request)
    {
        $id = $request->post('id');
        $delete = TopdealDetail::where([
          'id' => $id
        ])->delete();
        if (!empty($delete)) {
          $r['status'] = '200';
        } else {
          $r['status'] = '400';
        }
        return json_encode($r);
    }
}
