<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use App\Models\WebConfig;
use App\Models\User;
use App\Models\UserActivity;

class WebConfirmationController extends Controller
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

    protected function UserActivity($u_id, $activity)
    {
        UserActivity::create([
            'user_id' => $u_id,
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
        $ecommerce_url = DB::table('web_configs')->select('config_value')
        ->where('config_name', 'ecommerce_url')->first()->config_value;
        $title = WebConfig::select('config_value')->where('config_name', 'app_title')->get()->first()->config_value;
        $data = [
            'title' => $title,
            'subtitle' => DB::table('menu_accesses')->where('ma_slug', '=', request()->segment(1))->first()->ma_title,
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'ecommerce_url' => $ecommerce_url,
            'segment' => request()->segment(1),
        ];
        return view('app.web_confirmation.web_confirmation', compact('data'));
    }

    public function getDatatables(Request $request)
    {
        if(request()->ajax()) {
            return datatables()->of(DB::table('confirmations')->select('confirmations.id as id', 'pos_transactions.id as pt_id', 'pos_invoice', 'cf_name', 'cf_transfer', 'cf_bank_transfer', 'cf_ip', 'cf_read', 'cf_status', 'u_name', 'confirmations.created_at as created_at')
            ->leftJoin('pos_transactions', 'pos_transactions.id', '=', 'confirmations.pt_id')
            ->leftJoin('users', 'users.id', '=', 'confirmations.u_id'))
            ->editColumn('cf_transfer_show', function($data){
                $ecommerce_url = DB::table('web_configs')->select('config_value')
                ->where('config_name', 'ecommerce_url')->first()->config_value;
                
                if (!empty($data->cf_transfer)) {
                  $transfer = "<a href='".$ecommerce_url."/api/confirmation/600/".$data->cf_transfer."' target='_blank'><img src='".$ecommerce_url."/api/confirmation/600/".$data->cf_transfer."' style='width:100px;'/></a>";
                } else {
                  $transfer = '-';
                }
                return $transfer;
            })
            ->editColumn('pos_invoice_show', function($data){
                $ecommerce_url = DB::table('web_configs')->select('config_value')
                ->where('config_name', 'ecommerce_url')->first()->config_value;

                return "<a class='btn-sm btn-primary' href='".$ecommerce_url."/customer/order/detail/id/data/".$data->pt_id."' target='_blank'>".$data->pos_invoice."</a>";
            })
            ->editColumn('created_at_show', function($data){
                return date('d/m/Y', strtotime($data->created_at));
            })
            ->editColumn('cf_read_show', function($data){
                if ($data->cf_read == '0') {
                  $cf_read = "<a class='btn-sm btn-primary'>Belum</a>";
                } else {
                  $cf_read = "<a class='btn-sm btn-success'>Sudah</a>";
                }
                return $cf_read;
            })
            ->editColumn('cf_status_show', function($data){
                if ($data->cf_status == '0') {
                  $status = "<a class='btn-sm btn-warning' style='white-space:nowrap;'>Menunggu Konfirmasi</a>";
                } else if ($data->cf_status == '1') {
                  $status = "<a class='btn-sm btn-success'>Diterima</a>";
                } else {
                  $status = "<a class='btn-sm btn-danger'>Ditolak</a>";
                }
                return $status;
            })
            ->rawColumns(['cf_transfer_show', 'cf_read_show', 'cf_status_show', 'pos_invoice_show'])
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhereRaw('CONCAT(pos_invoice) LIKE ?', "%$search%");
                    });
                }
                if ($request->get('filter') != '') {
                    $instance->where(function($w) use($request){
                        $w->where('cf_status', '=', $request->get('filter'));
                    });
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function storeData(Request $request)
    {
        $mode = $request->input('_mode');
        $id = $request->input('_id');
        $pos_invoice = $request->input('pos_invoice');
        $data = [
            'cf_status' => $request->input('cf_status')
        ];
        $update = DB::table('confirmations')->where('id', '=', $id)->update($data);
        if ($update) {
            if ($request->input('cf_status') == '1') {
              $update_pos = DB::table('pos_transactions')->where('pos_invoice', '=', $pos_invoice)->update([
                'pos_status' => 'PAID',
                'created_at' => date('Y-m-d H:i:s')
              ]);
            } else {
              // tolak balik stok
              $pt_id = DB::table('pos_transactions')->select('id')->where('pos_invoice', '=', $pos_invoice)->get()->first()->id;
              $pos_td = DB::table('pos_transaction_details')->select('pl_id', 'pst_id', 'pos_td_qty')->where('pt_id', '=', $pt_id)->get();
              if (!empty($pos_td)) {
                foreach ($pos_td as $row) {
                  $pls_id = DB::table('product_location_setups')->select('id', 'pls_qty')->where([
                    'pl_id' => $row->pl_id,
                    'pst_id' => $row->pst_id
                  ])->get()->first();
                  $pls_qty = $pls_id->pls_qty;
                  if ($pls_qty < 0) {
                    $pls_qty = 0;
                  }
                  $update_pls = DB::table('product_location_setups')->where('id', '=', $pls_id->id)->update([
                    'pls_qty' => ($pls_qty + $row->pos_td_qty)
                  ]);
                }
                $update_pos = DB::table('pos_transactions')->where('pos_invoice', '=', $pos_invoice)->update([
                  'pos_status' => 'CANCEL'
                ]);
                $plst = DB::table('product_location_setup_transactions')->where('pt_id', '=', $pt_id)->update([
                  'plst_status' => 'INSTOCK'
                ]);
                $del_carts = DB::table('carts')->where('pt_id', '=', $pt_id)->delete();
              }
            }
            $this->UserActivity(Auth::user()->id, 'mengubah status konfirmasi invoice '.$request->input('pos_invoice'));
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function deleteData(Request $request)
    {
        $id = $request->input('_id');
        $delete = DB::table('confirmations')->where('id', '=', $id)->delete();
        if ($delete) {
            $this->UserActivity(Auth::user()->id, 'menghapus konfirmasi invoice '.$request->input('pos_invoice'));
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function readData(Request $request)
    {
        $id  = $request->input('_id');
        $update = DB::table('confirmations')->where('id', '=', $id)->update([
          'u_id' => Auth::user()->id,
          'cf_read' => '1'
        ]);
        if ($update) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }
}
