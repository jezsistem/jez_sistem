<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\WebConfig;
use App\Models\User;
use App\Models\PosTransaction;
use App\Models\PosTransactionDetail;
use App\Models\PosShippingInformation;
use App\Models\StoreTypeDivision;
use App\Models\Courier;
use App\Models\Store;
use App\Models\UserActivity;
use Image;
use File;

class InvoiceTrackingController extends Controller
{
    protected function validateAccess()
    {
        $segment = request()->segment(1);
        $slug = str_replace('_v2', '', $segment); // Strip _v2 suffix
        $validate = DB::table('user_menu_accesses')
            ->leftJoin('menu_accesses', 'menu_accesses.id', '=', 'user_menu_accesses.ma_id')->where([
                'u_id' => Auth::user()->id,
                'ma_slug' => $slug
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

    protected function getLabel($table, $field, $id)
    {
        $label = DB::table($table)->select($field)->where('id', '=', $id)->get()->first();
        if (!empty($label)) {
            return $label->$field;
        } else {
            return '[field not found]';
        }
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
        $path = "
        <li class='breadcrumb-item'>
            <a href='' class='text-muted'>Invoice Tracking</a>
        </li>";
        $data = [
            'title' => $title,
            'subtitle' => DB::table('menu_accesses')->where('ma_slug', '=', request()->segment(1))->first()->ma_title,
            'sidebar' => $this->sidebar(),
            'path' => $path,
            'user' => $user_data,
            'segment' => request()->segment(1),
            'st_id' => Store::where('st_delete', '!=', '1')->orderByDesc('id')->pluck('st_name', 'id'),
            'std_id' => StoreTypeDivision::where('dv_delete', '!=', '1')->orderByDesc('id')->pluck('dv_name', 'id'),
        ];
        return view('app.invoice_tracking.invoice_tracking', compact('data'));
    }

    public function indexUpdated()
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
            'subtitle' => DB::table('menu_accesses')->where('ma_slug', '=', str_replace('_v2', '', request()->segment(1)))->first()->ma_title,
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1),
            'st_id' => Store::where('st_delete', '!=', '1')->orderByDesc('id')->pluck('st_name', 'id'),
            'std_id' => StoreTypeDivision::where('dv_delete', '!=', '1')->orderByDesc('id')->pluck('dv_name', 'id'),
        ];
        return view('app.updated_invoice_tracking.invoice_tracking', compact('data'));
    }

    public function getDatatables(Request $request)
    {
        if (!empty($request->st_id)) {
            $st_id = $request->st_id;
        } else {
            $st_id = Auth::user()->st_id;
        }
        $date = $request->get('date');
        $start = null;
        $end = null;
        $exp = explode('|', $date);
        $total = count($exp);
        if ($total > 1) {
            if ($exp[0] != $exp[1]) {
                $start = $exp[0];
                $end = $exp[1];
            } else {
                $start = $exp[0];
            }
        } else {
            if (!empty($date)) {
                $start = $date;
            } else {
                $start = date('Y-m-d');
            }
        }


        if (request()->ajax()) {
            return datatables()->of(PosTransaction::selectRaw("ts_pos_transactions.id as pt_id, sum(ts_pos_transaction_details.pos_td_qty) as total_item, u_name, cust_name, cust_id, pos_invoice, std_id, stt_name, pos_real_price, dv_name, cr_id, pt_id_ref, pos_shipping_number, psi_courier, psi_description, ts_pos_transactions.created_at as pos_created, pos_status, pos_payment")
                ->leftJoin('store_types', 'store_types.id', '=', 'pos_transactions.stt_id')
                ->leftJoin('store_type_divisions', 'store_type_divisions.id', '=', 'pos_transactions.std_id')
                ->leftJoin('pos_shipping_information', 'pos_shipping_information.pt_id', '=', 'pos_transactions.id')
                ->leftJoin('pos_transaction_details', 'pos_transaction_details.pt_id', '=', 'pos_transactions.id')
                ->leftJoin('customers', 'customers.id', '=', 'pos_transactions.cust_id')
                ->leftJoin('users', 'users.id', '=', 'pos_transactions.u_id')
                ->where('pos_transactions.st_id', '=', $st_id)
                ->where(function ($w) use ($start, $end) {
                    if (!empty($end)) {
                        $w->whereDate('pos_transactions.created_at', '>=', $start)
                            ->whereDate('pos_transactions.created_at', '<=', $end);
                    } else {
                        $w->whereDate('pos_transactions.created_at', '=', $start);
                    }
                })
                ->groupBy('pos_transactions.id'))
                ->editColumn('pos_invoice', function ($data) {
                    if (!empty($data->pt_id_ref)) {
                        $invoice = PosTransaction::select('pos_invoice')->where('id', $data->pt_id_ref)->get()->first()->pos_invoice;

                        if (strtoupper($data->stt_name) == 'ONLINE' && substr(trim((string) $data->pos_invoice), 0, 3) === 'INV') {
                            return '<a class="text-white" href="' . url('/') . '/print_invoice/' . $data->pos_invoice . '" target="_blank"><span class="btn btn-sm btn-warning" title="' . $invoice . '">' . $data->pos_invoice . '</span></a>';
                        } else {
                            return '<a class="text-white" href="' . url('/') . '/print_offline_invoice/' . $data->pos_invoice . '" target="_blank"><span class="btn btn-sm btn-warning" title="' . $invoice . '">' . $data->pos_invoice . '</span></a>';
                        }
                    } else {


                        if (strtoupper($data->stt_name) == 'ONLINE' && substr(trim((string) $data->pos_invoice), 0, 3) === 'INV') {
                            if ($data->pos_status == 'DONE' || $data->pos_status == 'PAID') {
                                $return = '<a class="text-white" href="' . url('/') . '/print_invoice/' . $data->pos_invoice . '" target="_blank"><span class="btn btn-sm btn-primary">' . $data->pos_invoice . '</span></a>';
                            } else {
                                $return = '<button class="text-white btn btn-sm btn-primary">' . $data->pos_invoice . '</button>';
                            }
                            return $return;
                        } else if (strtoupper($data->stt_name) == 'ONLINE' && substr(trim((string) $data->pos_invoice), 0, 3) !== 'INV') { // NOT INV
                            return '<a class="text-white" href="' . url('/') . '/print_online_nota/' . $data->pos_invoice . '" target="_blank"><span class="btn btn-sm btn-primary">' . $data->pos_invoice . '</span></a>';
                        } else {
                            return '<a class="text-white" href="' . url('/') . '/print_offline_invoice/' . $data->pos_invoice . '" target="_blank"><span class="btn btn-sm btn-primary">' . $data->pos_invoice . '</span></a>';
                        }
                        return '';
                    }
                })
                ->editColumn('pos_created', function ($data) {
                    return '<span style="white-space: nowrap;">' . date('d-m-Y H:i:s', strtotime($data->pos_created)) . '</span>';
                })
                ->editColumn('pos_shipping_number', function ($data) {
                    if (!empty($data->pos_shipping_number)) {
                        return '<span class="btn-sm btn-primary" style="white-space:nowrap;" data-id="' . $data->pt_id . '" id="waybill_tracking_btn">' . $data->pos_shipping_number . '</span>';
                    } else {
                        return '-';
                    }
                })
                ->editColumn('pos_shipment', function ($data) {
                    if (!empty($data->psi_description)) {
                        return $data->psi_description;
                    } else {
                        return '-';
                    }
                })
                ->editColumn('pos_status', function ($data) {
                    $ref_invoice = '';
                    if ($data->pos_status == 'DONE' || $data->pos_status == 'PAID') {
                        if (!empty($data->pt_id_ref)) {
                            $ref_invoice = PosTransaction::select('pos_invoice')->where('id', $data->pt_id_ref)->get()->first()->pos_invoice;
                            $btn = 'btn-warning';
                        } else {
                            $btn = 'btn-success';
                        }
                    }
                    if ($data->pos_status == 'REFUND') {
                        $btn = 'btn-danger';
                    }
                    if ($data->pos_status == 'EXCHANGE') {
                        $btn = 'btn-danger';
                    }
                    if ($data->pos_status == 'NAMESET') {
                        $btn = 'btn-info';
                    }
                    if ($data->pos_status == 'SHIPPING NUMBER') {
                        $btn = 'btn-warning';
                    }
                    if ($data->pos_status == 'IN DELIVERY') {
                        $btn = 'btn-info';
                    }
                    if ($data->pos_status == 'WAITING FOR CONFIRMATION') {
                        $btn = 'btn-warning';
                    }
                    if ($data->pos_status == 'DP') {
                        return '<span class="btn btn-sm btn-warning"
                            data-pt_id ="' . $data->pt_id . '" id="dp_payment_btn" 
                            data-pos_real_price="' . $data->pos_real_price . '"
                            data-pos_payment="' . $data->pos_payment . '"
                            >' . $data->pos_status . '</span>';
                    }
                    if ($data->pos_status == 'IN PROGRESS') {
                        $btn = 'btn-primary';
                    }
                    if ($data->pos_status == 'CANCEL' || $data->pos_status == 'UNPAID' || $data->pos_status == 'REJECTED') {
                        $btn = 'btn-danger';
                    }
                    if ($data->pos_status == 'SHIPPING NUMBER' || $data->pos_status == 'IN DELIVERY') {
                        $img = '';
                        $check = DB::table('pos_images')->where('pt_id', '=', $data->pt_id)
                            ->whereNotNull('image')->first();
                        if (!empty($check)) {
                            $img = $check->image;
                        }
                        return '<span style="white-space: nowrap;" data-img="' . $img . '" data-pt_id="' . $data->pt_id . '" data-cust_id="' . $data->cust_id . '" class="btn btn-sm ' . $btn . '" id="shipping_number_btn">' . $data->pos_status . ' ' . $ref_invoice . '</span>';
                    } else {
                        return '<span style="white-space: nowrap;" title="' . $ref_invoice . '" class="btn btn-sm ' . $btn . '">' . $data->pos_status . '</span>';
                    }
                })
                ->rawColumns(['pos_invoice', 'pos_shipping_number', 'stt_name', 'pos_status', 'pos_created'])
                ->filter(function ($instance) use ($request) {
                    if (!empty($request->get('status'))) {
                        $instance->where('pos_status', $request->get('status'));
                    }
                    if (!empty($request->get('division'))) {
                        $instance->where('std_id', $request->get('division'));
                    }
                    if (!empty($request->get('search'))) {
                        $instance->where(function ($w) use ($request) {
                            $search = $request->get('search');
                            $w->orWhere('pos_invoice', 'LIKE', "%$search%")
                                ->orWhere('pos_shipping_number', 'LIKE', "%$search%")
                                ->orWhere('cust_name', 'LIKE', "%$search%");
                        });
                    }
                })
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function updateData(Request $request)
    {
        $shipping_number = $request->pos_shipping_number;
        $pt_id = $request->_id;
        $cust_id = $request->_cust_id;
        $courier = $request->courier;
        // $image = '';

        try {
            $update = PosTransaction::where('id', $pt_id)->update([
                'pos_shipping_number' => str_replace(' ', '', $shipping_number),
                'pos_resi' => str_replace(' ', '', $shipping_number),
                'cr_id' => $courier,
            ]);

            if ($request->hasFile('imageResi')) {
                $file = $request->file('imageResi');
                $filename = 'resi_' .  $pt_id . '.' . $file->getClientOriginalExtension();

                $file->move(public_path('upload/resi'), $filename);

                DB::table('pos_transactions')->where('id',  $pt_id)->update([
                    'pos_resi_file' =>  $filename
                ]);
            }

            if (!empty($update)) {
                $r['status'] = '200';
                $cs = DB::table('customers')->select('cust_name', 'cust_phone')->where('id', '=', $cust_id)->get()->first();
                $invoice = PosTransaction::select('pos_invoice')->where('id', '=', $pt_id)->get()->first()->pos_invoice;
                $this->waSend($cs->cust_phone, $cs->cust_name, $invoice, strtoupper($courier), $shipping_number);
            } else {
                $r['status'] = '400';
            }
        } catch (\Exception $e) {
            $r['status'] = '500';
            $r['error'] = $e->getMessage();
        }
        return json_encode($r);
    }

    private function waSend($phone, $name, $invoice, $courier, $resi)
    {
        $phone = str_replace('+', '', $phone);
        $check = substr($phone, 0, 1);
        if ($check == '0' || $check == 0) {
            $phone = preg_replace('/^0/', '62', $phone);
        }
        $message = "Halo *" . $name . "*!

Terima kasih telah berbelanja.
Kami telah mengirimkan pesanan anda *#" . $invoice . "* melalui kurir *" . $courier . "*.

Berikut nomor resinya:
" . $resi . "

Terimakasih
------------------------
Balas pesan ini jika butuh bantuan :)";
        $wablas_endpoint = DB::table('web_configs')->select('config_value')
            ->where('config_name', 'wablas_endpoint')->first()->config_value;
        $wablas_api = DB::table('web_configs')->select('config_value')
            ->where('config_name', 'wablas_api')->first()->config_value;

        $curl = curl_init();

        $data = [
            'phone' => $phone,
            'message' => $message,
        ];
        curl_setopt(
            $curl,
            CURLOPT_HTTPHEADER,
            array(
                "Authorization: $wablas_api",
            )
        );
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($curl, CURLOPT_URL, $wablas_endpoint . "/api/send-message");
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

        $result = curl_exec($curl);
        curl_close($curl);
        $getdata = json_decode($result);
        if ($getdata->status == 'pending') {
            return 'Success';
        } else {
            return 'Fail';
        }
    }

    public function waybillTracking(Request $request)
    {
        $waybill_number = $request->_waybill_number;
        $id = $request->_id;
        $courier = PosShippingInformation::select('psi_courier')
            ->leftJoin('pos_transactions', 'pos_transactions.id', '=', 'pos_shipping_information.pt_id')->where('pos_shipping_number', '=', $waybill_number)->get()->first();
        if (!empty($courier)) {
            $waybill = $this->shipmentracking($waybill_number, $courier->psi_courier, $id);
        } else {
            $waybill = $this->shipmentracking($waybill_number, '', $id);
        }
        $trace = '';
        if ($waybill['status'] == '200') {
            $description = $waybill['data']['summary']['status'];
            $pt_id = PosTransaction::select('id')->where('pos_shipping_number', $waybill_number)->get()->first()->id;
            if ($description == 'DELIVERED') {
                PosTransaction::where('id', $pt_id)
                    ->whereNotIn('pos_status', ['DONE', 'EXCHANGE', 'REFUND'])->update([
                        'pos_status' => 'DONE'
                    ]);
            } else {
                PosTransaction::where('id', $pt_id)
                    ->whereNotIn('pos_status', ['DONE', 'EXCHANGE', 'REFUND'])->update([
                        'pos_status' => 'DONE'
                    ]);
            }
            PosShippingInformation::leftJoin('pos_transactions', 'pos_transactions.id', '=', 'pos_shipping_information.pt_id')
                ->where('pos_shipping_number', '=', $waybill_number)->update([
                    'psi_description' => $description
                ]);
            $trace .= '<table class="table table-hover table-checkable">';
            $trace .= '<tbody>';
            $trace .= '<tr>';
            $trace .= '<td>Resi</td><td>' . $waybill['data']['summary']['awb'] . '</td>';
            $trace .= '</tr>';
            $trace .= '<tr>';
            $trace .= '<td>Pengirim</td><td>' . $waybill['data']['detail']['shipper'] . '</td>';
            $trace .= '</tr>';
            $trace .= '<tr>';
            $trace .= '<td>Lokasi Pengirim</td><td>' . $waybill['data']['detail']['origin'] . '</td>';
            $trace .= '</tr>';
            $trace .= '<tr>';
            $trace .= '<td>Penerima</td><td>' . $waybill['data']['detail']['receiver'] . '</td>';
            $trace .= '</tr>';
            $trace .= '<tr>';
            $trace .= '<td>Tujuan</td><td>' . $waybill['data']['detail']['destination'] . '</td>';
            $trace .= '</tr>';
            $trace .= '<tr>';
            $trace .= '<td>Status</td><td>' . $waybill['data']['summary']['status'] . '</td>';
            $trace .= '</tr>';
            $trace .= '</tbody>';

            $trace .= '<table class="table table-hover table-checkable">';
            $trace .= '<thead>';
            $trace .= '<th>Tanggal/Waktu</th>';
            $trace .= '<th>Deskripsi</th>';
            $trace .= '<th>Lokasi</th>';
            $trace .= '</thead>';
            $trace .= '<tbody>';
            foreach ($waybill['data']['history'] as $row) {
                $trace .= '<tr>';
                $trace .= '<td style="white-space:nowrap;">' . date('d/m/Y H:i:s', strtotime($row['date'])) . '</td><td>' . $row['desc'] . '</td><td style="white-space:nowrap;">' . $row['location'] . '</td>';
                $trace .= '</tr>';
            }
            $trace .= '</tbody>';
            $trace .= '</table>';
            echo $trace;
        } else {
            echo "Terjadi error, atau resi kurir belum support";
        }
    }

    public function invoiceDpRepayment(Request $request)
    {
        try {
            $id = $request->_pt_id;
            $payment_dp = $request->payment_dp;
            $payment_dp_date = $request->payment_dp_date;
            $payment_method = $request->payment_method;
            $sub_payment = $request->sub_payment;
            $notes = $request->payment_notes;

            $pt_update = PosTransaction::where('id', $id)->update([
                'pm_id_partial' => $payment_method,
                'pos_payment_partial' => $payment_dp,
                'sub_payment_partial' => $sub_payment,
                'pos_paid_dp' => $payment_dp,
                'pos_paid_dp_date' => $payment_dp_date,
                'pos_notes_dp' => $notes,
                'pos_status' => 'DONE'
            ]);

            if ($pt_update) {
                $r['status'] = '200';
                $r['message'] = 'Pembayaran DP Berhasil Disimpan';
            } else {
                $r['status'] = '400';
                $r['message'] = 'Gagal menyimpan pembayaran DP';
            }

            return json_encode($r);
        } catch (\Exception $e) {
            return json_encode($e->getMessage());
        }
    }

    public function invoiceDpRepaymentDetails($id)
    {
        $pt = PosTransaction::where('pos_transactions.id', $id)
        ->select('pos_transactions.pos_real_price', 'pos_transactions.pos_payment', 'pos_transactions.pos_note', 'pos_transactions.created_at', 'payment_methods.pm_name', 'pos_transactions.st_id')
        ->leftJoin('payment_methods', 'payment_methods.id', '=', 'pos_transactions.pm_id')
        ->first();
        $payment_methods = DB::table('payment_methods')->where('pm_delete', '!=', '1') ->where('st_id', $pt->st_id)->orderBy('pm_name')->pluck('pm_name', 'id')->toArray();

        $data = [
            'total_sales' => $pt->pos_real_price,
            'payment_1' => $pt->pos_payment,
            'outstanding' => $pt->pos_real_price - $pt->pos_payment,
            'paydate' => date('d F Y H:i', strtotime($pt->created_at)),
            'payment_method_1' => $pt->pm_name,
            'notes' => $pt->pos_note,

            'payment_methods' => $payment_methods
        ];

        if ($data) {
            return response()->json([
                'status' => '200',
                'data' => $data
            ]);
        } else {
            return response()->json([
                'status' => '400',
                'data' => null
            ]);
        }
    }

    private function shipmentracking($shipping_number, $courier, $id)
    {
        $binderbyte_api = DB::table('web_configs')->select('config_value')
            ->where('config_name', 'binderbyte_api')->first()->config_value;

        if (!empty($courier)) {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.binderbyte.com/v1/track?api_key=$binderbyte_api&courier=$courier&awb=$shipping_number",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET"
            ));
            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);
            if ($err) {
                return "cURL Error #:" . $err;
            } else {
                return json_decode($response, true);
            }
        } else {
            $arr = ['jne', 'pos', 'jnt', 'sicepat', 'tiki', 'anteraja', 'wahana', 'ninja', 'lion', 'pcp', 'jet', 'rex', 'sap', 'jxe', 'rpx', 'first', 'ide', 'spx', 'kgx'];
            for ($i = 0; $i < count($arr); $i++) {
                $status = null;
                $status = null;
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => "https://api.binderbyte.com/v1/track?api_key=$binderbyte_api&courier=$arr[$i]&awb=$shipping_number",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "GET"
                ));
                $response = curl_exec($curl);
                $err = curl_error($curl);
                curl_close($curl);
                if ($err) {
                    $status = 'error';
                } else {
                    $status = json_decode($response, true);
                }
                if ($status != 'error') {
                    if ($status['status'] == '200') {
                        $description = $status['data']['summary']['status'];
                        DB::table('pos_shipping_information')->insert([
                            'pt_id' => $id,
                            'psi_courier' => $arr[$i],
                            'psi_description' => $description
                        ]);
                        return json_decode($response, true);
                        break;
                    }
                } else {
                    return '400';
                }
            }
        }
    }
}
