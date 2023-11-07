<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use App\Models\WebConfig;
use App\Models\User;
use App\Models\PosTransaction;
use App\Models\PosTransactionDetail;
use App\Models\UserActivity;

class WebTransactionController extends Controller
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
        return view('app.web_transaction.web_transaction', compact('data'));
    }

    public function getDatatables(Request $request)
    {
        if(request()->ajax()) {
            return datatables()->of(PosTransaction::select('pos_transactions.id as id', 'pos_web_notif', 'cust_first', 'cust_second', 'cust_third', 'cust_name', 'pos_note', 'pos_real_price', 'pos_web_payment', 'pos_unique_code', 'pos_invoice', 'pos_shipping', 'pos_shipping_number', 'pos_courier', 'pos_status', 'pos_transactions.created_at')
            ->leftJoin('customers', 'customers.id', '=', 'pos_transactions.cust_id')
            ->where('is_website', '=', '1'))
            ->editColumn('item', function($data){
                $item = PosTransactionDetail::where('pt_id', '=', $data->id)->sum('pos_td_qty');
                return $item;
            })
            ->editColumn('pos_invoice_show', function($data){
                $ecommerce_url = DB::table('web_configs')->select('config_value')
                ->where('config_name', 'ecommerce_url')->first()->config_value;

                return "<a class='btn-sm btn-primary' href='".$ecommerce_url."/customer/order/detail/id/data/".$data->id."' target='_blank'>".$data->pos_invoice."</a>";
            })
            ->editColumn('pos_status_show', function($data){
                $danger = ['UNPAID', 'CANCEL', 'REFUND', 'EXCHANGE'];
                $success = ['DONE', 'PAID'];
                $shipping = ['SHIPPING NUMBER'];
                if (in_array($data->pos_status, $danger)) {
                  if ($data->pos_status == 'UNPAID') {
                    $btn = "<a class='btn btn-sm btn-danger' id='unpaid_btn'>".$data->pos_status."</a>";
                  } else {
                    $btn = "<a class='btn btn-sm btn-danger'>".$data->pos_status."</a>";
                  }
                } else if (in_array($data->pos_status, $shipping)) {
                  $btn = "<a class='btn btn-sm btn-info' id='shipping_btn'>".$data->pos_status."</a>";
                } else {
                  $btn = "<a class='btn btn-sm btn-success'>".$data->pos_status."</a>";
                }
                return $btn;
            })
            ->editColumn('created_at_show', function($data){
                return date('d/m/Y H:i:s', strtotime($data->created_at));
            })
            ->editColumn('diff_time', function($data){
                $t1 = strtotime(date('Y-m-d H:i:s'));
                $t2 = strtotime($data->created_at);
                $diff = $t1 - $t2;
                $hours = round($diff / 3600);
                if ($hours < 12) {
                  return "<a class='btn-sm btn-primary'>".$hours."</a>";
                } else {
                  return "<a class='btn-sm btn-danger'>".$hours."</a>";
                }
            })
            ->editColumn('pos_note_show', function($data){
                if (!empty($data->pos_note)) {
                  return "<i class='fa fa-eye text-dark' title='".$data->pos_note."'></i>";
                } else {
                  return "-";
                }
            })
            ->editColumn('pos_web_notif_show', function($data){
                if ($data->pos_web_notif == '1') {
                  return "<a class='btn-sm btn-success'>Y</a>";
                } else {
                  return "<a class='btn-sm btn-danger'>N</a>";
                }
            })
            ->editColumn('cust_first_show', function($data){
                if ($data->cust_first == '1') {
                  return "<a class='btn-sm btn-success'>Y</a>";
                } else {
                  return "<a class='btn-sm btn-danger'>N</a>";
                }
            })
            ->editColumn('cust_second_show', function($data){
                if ($data->cust_second == '1') {
                  return "<a class='btn-sm btn-success'>Y</a>";
                } else {
                  return "<a class='btn-sm btn-danger'>N</a>";
                }
            })
            ->editColumn('cust_third_show', function($data){
                if ($data->cust_third == '1') {
                  return "<a class='btn-sm btn-success'>Y</a>";
                } else {
                  return "<a class='btn-sm btn-danger'>N</a>";
                }
            })
            ->rawColumns(['item', 'pos_status_show', 'pos_note_show', 'cust_first_show', 'cust_second_show', 'cust_third_show', 'pos_web_notif_show', 'created_at_show', 'diff_time', 'pos_invoice_show'])
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhereRaw('CONCAT(pos_invoice) LIKE ?', "%$search%")
                        ->orWhereRaw('CONCAT(cust_name) LIKE ?', "%$search%");
                    });
                }
                if (!empty($request->get('filter'))) {
                    $instance->where(function($w) use($request){
                        $w->where('pos_status', '=', $request->get('filter'));
                    });
                }
                if (!empty($request->get('payment'))) {
                    $instance->where(function($w) use($request){
                        $w->where('pos_web_payment', '=', $request->get('payment'));
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
        $data = [
            'pos_status' => strtolower($request->input('pos_status'))
        ];
        if ($request->input('pos_status') == 'PAID') {
          $update = DB::table('pos_transactions')->where('id', '=', $id)->update($data);
        } else {
          // tolak balik stok
          $pt_id = $id;
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
            $update = DB::table('pos_transactions')->where('id', '=', $pt_id)->update([
              'pos_status' => 'CANCEL'
            ]);
            $plst = DB::table('product_location_setup_transactions')->where('pt_id', '=', $pt_id)->update([
              'plst_status' => 'INSTOCK'
            ]);
            $del_carts = DB::table('carts')->where('pt_id', '=', $pt_id)->delete();
          }
        }
        $this->UserActivity(Auth::user()->id, 'mengubah status konfirmasi invoice dengan ID '.$id);
        if ($update) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }
}
