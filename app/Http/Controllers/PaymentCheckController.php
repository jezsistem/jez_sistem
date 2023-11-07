<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\Midtrans\CreateSnapTokenService;
use App\Models\UserActivity;

class PaymentCheckController extends Controller
{
    protected function UserActivity($u_id, $activity)
    {
        UserActivity::create([
            'user_id' => $u_id,
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

    private function deleteNullPTID() {
      $delete = DB::table('pos_transaction_details')->whereNull('pt_id')->delete();
    }

    public function checkData()
    {
      $this->deleteNullPTID();
       $get_invoice = DB::table('pos_transactions')->select('id', 'created_at', 'pos_invoice', 'pm_id', 'pos_status', 'cust_id', 'pos_url', 'pos_web_notif', 'pos_real_price')->where('pos_status', '=', 'UNPAID')->get();
       if (!empty($get_invoice)) {
         foreach ($get_invoice as $row) {
            $check_ptd = DB::table('pos_transaction_details')->where('pt_id', '=', $row->id)->exists();
            if (!$check_ptd) {
                $delete = DB::table('pos_transactions')->where('id', '=', $row->id)->delete();
                continue;
            }
           $t1 = strtotime(date('Y-m-d H:i:s'));
           $t2 = strtotime($row->created_at);
           $diff = $t1 - $t2;
           $hours = round($diff / 3600);
           if ($row->pm_id == '2') {
              $confirmation = DB::table('confirmations')->where('pt_id', '=', $row->id)->exists();
              if (!$confirmation) {
                if ($hours >= 8) {
                  $pos_td = DB::table('pos_transaction_details')->select('pl_id', 'pst_id', 'pos_td_qty')->where('pt_id', '=', $row->id)->get();
                  if (!empty($pos_td)) {
                    foreach ($pos_td as $crow) {
                      $pls_id = DB::table('product_location_setups')->select('id', 'pls_qty')->where([
                        'pl_id' => $crow->pl_id,
                        'pst_id' => $crow->pst_id
                      ])->get()->first();
                      $pls_qty = $pls_id->pls_qty;
                      if ($pls_qty < 0) {
                        $pls_qty = 0;
                      }
                      $update_pls = DB::table('product_location_setups')->where('id', '=', $pls_id->id)->update([
                        'pls_qty' => ($pls_qty + $crow->pos_td_qty)
                      ]);
                    }
                    $update_pos = DB::table('pos_transactions')->where('id', '=', $row->id)->update([
                      'pos_status' => 'CANCEL'
                    ]);
                    $plst = DB::table('product_location_setup_transactions')->where('pt_id', '=', $row->id)->update([
                      'plst_status' => 'INSTOCK'
                    ]);
                    $del_carts = DB::table('carts')->where('pt_id', '=', $row->id)->delete();
                  }
                } else {
                  $cs = DB::table('customers')->select('cust_name', 'cust_phone', 'cust_first', 'cust_second', 'cust_third')->where('id', '=', $row->cust_id)->get()->first();
                  if ($row->pos_web_notif == '0') {
                    $order_notif = $this->waSendOrder($cs->cust_phone, $cs->cust_name, $row->pos_invoice, $row->pos_real_price, $row->pos_url);
                    if ($order_notif == "Success") {
                      DB::table('pos_transactions')->where('id', '=', $row->id)->update([
                        'pos_web_notif' => '1'
                      ]);
                    }
                  }
                  if ($hours == '6' || $hours == '9' || $hours == '11') {
                    if (!empty($cs)) {
                      $left = (8-$hours);
                      if ($hours == '6' AND $cs->cust_first == '0') {
                        $reminder = $this->waSend($cs->cust_phone, $cs->cust_name, $row->pos_invoice, $row->pos_url, $hours, $left);
                        if ($reminder == "Success") {
                          DB::table('customers')->where('id', '=', $row->cust_id)->update([
                            'cust_first' => '1'
                          ]);
                        }
                      } else if ($hours == '9' AND $cs->cust_second == '0') {
                        $reminder = $this->waSend($cs->cust_phone, $cs->cust_name, $row->pos_invoice, $row->pos_url, $hours, $left);
                        if ($reminder == "Success") {
                          DB::table('customers')->where('id', '=', $row->cust_id)->update([
                            'cust_second' => '1'
                          ]);
                        }
                      } else if ($hours == '11' AND $cs->cust_third == '0') {
                        $reminder = $this->waSend($cs->cust_phone, $cs->cust_name, $row->pos_invoice, $row->pos_url, $hours, $left);
                        if ($reminder == "Success") {
                          DB::table('customers')->where('id', '=', $row->cust_id)->update([
                            'cust_third' => '1'
                          ]);
                        }
                      }
                    }
                  }
                }
              }
           } else if ($row->pm_id == '8') {
              $midtrans = $this->getMidtransStatus($row->pos_invoice);
              if ($midtrans != '400') {
                if ($midtrans['status_code'] == '404') {
                  if ($hours >= 8) {
                    // cancel
                    $pos_td = DB::table('pos_transaction_details')->select('pl_id', 'pst_id', 'pos_td_qty')->where('pt_id', '=', $row->id)->get();
                    if (!empty($pos_td)) {
                      foreach ($pos_td as $crow) {
                        $pls_id = DB::table('product_location_setups')->select('id', 'pls_qty')->where([
                          'pl_id' => $crow->pl_id,
                          'pst_id' => $crow->pst_id
                        ])->get()->first();
                        $pls_qty = $pls_id->pls_qty;
                        if ($pls_qty < 0) {
                          $pls_qty = 0;
                        }
                        $update_pls = DB::table('product_location_setups')->where('id', '=', $pls_id->id)->update([
                          'pls_qty' => ($pls_qty + $crow->pos_td_qty)
                        ]);
                      }
                      $update_pos = DB::table('pos_transactions')->where('id', '=', $row->id)->update([
                        'pos_status' => 'CANCEL'
                      ]);
                      $plst = DB::table('product_location_setup_transactions')->where('pt_id', '=', $row->id)->update([
                        'plst_status' => 'INSTOCK'
                      ]);
                      $del_carts = DB::table('carts')->where('pt_id', '=', $row->id)->delete();
                    }
                    continue;
                  } else {
                    $cs = DB::table('customers')->select('cust_name', 'cust_phone', 'cust_first', 'cust_second', 'cust_third')->where('id', '=', $row->cust_id)->get()->first();
                    if ($row->pos_web_notif == '0') {
                      $order_notif = $this->waSendOrder($cs->cust_phone, $cs->cust_name, $row->pos_invoice, $row->pos_real_price, $row->pos_url);
                      if ($order_notif == "Success") {
                        DB::table('pos_transactions')->where('id', '=', $row->id)->update([
                          'pos_web_notif' => '1'
                        ]);
                      }
                    }
                    if ($hours == '6' || $hours == '9' || $hours == '11') {
                      if (!empty($cs)) {
                        $left = (8-$hours);
                        if ($hours == '6' AND $cs->cust_first == '0') {
                          $reminder = $this->waSend($cs->cust_phone, $cs->cust_name, $row->pos_invoice, $row->pos_url, $hours, $left);
                          if ($reminder == "Success") {
                            DB::table('customers')->where('id', '=', $row->cust_id)->update([
                              'cust_first' => '1'
                            ]);
                          }
                        } else if ($hours == '9' AND $cs->cust_second == '0') {
                          $reminder = $this->waSend($cs->cust_phone, $cs->cust_name, $row->pos_invoice, $row->pos_url, $hours, $left);
                          if ($reminder == "Success") {
                            DB::table('customers')->where('id', '=', $row->cust_id)->update([
                              'cust_second' => '1'
                            ]);
                          }
                        } else if ($hours == '11' AND $cs->cust_third == '0') {
                          $reminder = $this->waSend($cs->cust_phone, $cs->cust_name, $row->pos_invoice, $row->pos_url, $hours, $left);
                          if ($reminder == "Success") {
                            DB::table('customers')->where('id', '=', $row->cust_id)->update([
                              'cust_third' => '1'
                            ]);
                          }
                        }
                      }
                    }
                    continue;
                  }
                }
                if ($midtrans['transaction_status'] == 'settlement') {
                  // ubah jadi paid
                  DB::table('pos_transactions')->where('id', '=', $row->id)->update([
                    'pos_status' => 'PAID',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                  ]);
                  DB::table('pos_transaction_details')->where('pt_id', '=', $row->id)->update([
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                  ]);
                } else {
                  if ($hours >= 8) {
                    // cancel
                    $pos_td = DB::table('pos_transaction_details')->select('pl_id', 'pst_id', 'pos_td_qty')->where('pt_id', '=', $row->id)->get();
                    if (!empty($pos_td)) {
                      foreach ($pos_td as $crow) {
                        $pls_id = DB::table('product_location_setups')->select('id', 'pls_qty')->where([
                          'pl_id' => $crow->pl_id,
                          'pst_id' => $crow->pst_id
                        ])->get()->first();
                        $pls_qty = $pls_id->pls_qty;
                        if ($pls_qty < 0) {
                          $pls_qty = 0;
                        }
                        $update_pls = DB::table('product_location_setups')->where('id', '=', $pls_id->id)->update([
                          'pls_qty' => ($pls_qty + $crow->pos_td_qty)
                        ]);
                      }
                      $update_pos = DB::table('pos_transactions')->where('id', '=', $row->id)->update([
                        'pos_status' => 'CANCEL'
                      ]);
                      $plst = DB::table('product_location_setup_transactions')->where('pt_id', '=', $row->id)->update([
                        'plst_status' => 'INSTOCK'
                      ]);
                      $del_carts = DB::table('carts')->where('pt_id', '=', $row->id)->delete();
                    }
                  } else {
                    $cs = DB::table('customers')->select('cust_name', 'cust_phone', 'cust_first', 'cust_second', 'cust_third')->where('id', '=', $row->cust_id)->get()->first();
                    if ($row->pos_web_notif == '0') {
                      $order_notif = $this->waSendOrder($cs->cust_phone, $cs->cust_name, $row->pos_invoice, $row->pos_real_price, $row->pos_url);
                      if ($order_notif == "Success") {
                        DB::table('pos_transactions')->where('id', '=', $row->id)->update([
                          'pos_web_notif' => '1'
                        ]);
                      }
                    }
                    if ($hours == '6' || $hours == '9' || $hours == '11') {
                      if (!empty($cs)) {
                        $left = (8-$hours);
                        if ($hours == '6' AND $cs->cust_first == '0') {
                          $reminder = $this->waSend($cs->cust_phone, $cs->cust_name, $row->pos_invoice, $row->pos_url, $hours, $left);
                          if ($reminder == "Success") {
                            DB::table('customers')->where('id', '=', $row->cust_id)->update([
                              'cust_first' => '1'
                            ]);
                          }
                        } else if ($hours == '9' AND $cs->cust_second == '0') {
                          $reminder = $this->waSend($cs->cust_phone, $cs->cust_name, $row->pos_invoice, $row->pos_url, $hours, $left);
                          if ($reminder == "Success") {
                            DB::table('customers')->where('id', '=', $row->cust_id)->update([
                              'cust_second' => '1'
                            ]);
                          }
                        } else if ($hours == '11' AND $cs->cust_third == '0') {
                          $reminder = $this->waSend($cs->cust_phone, $cs->cust_name, $row->pos_invoice, $row->pos_url, $hours, $left);
                          if ($reminder == "Success") {
                            DB::table('customers')->where('id', '=', $row->cust_id)->update([
                              'cust_third' => '1'
                            ]);
                          }
                        }
                      }
                    }
                  }
                }
              }
           }
         }
       }
    }

private function waSendOrder($phone, $name, $invoice, $total, $pos_url)
   {
      $phone = str_replace('+', '', $phone);
      $check = substr($phone, 0, 1);
      if ($check == '0' || $check == 0) {
          $phone = preg_replace('/^0/', '62', $phone);
      }
       $message = "Halo *".$name."*!

Terima kasih telah berbelanja.
Kami telah menerima order anda dengan Invoice *#".$invoice."*.

Total yang harus dibayar adalah:
Rp ".$total."

Silahkan lakukan pembayaran pada link pesanan anda berikut ini, klik:

".$pos_url."
------------------------
Balas pesan ini jika butuh bantuan atau link tidak bisa diklik :)";

      $wablas_endpoint = DB::table('web_configs')->select('config_value')
      ->where('config_name', 'wablas_endpoint')->first()->config_value;
      $wablas_api = DB::table('web_configs')->select('config_value')
      ->where('config_name', 'wablas_api')->first()->config_value;

      $curl = curl_init();
      $data = [
          'phone' => $phone,
          'message' => $message,
      ];
      curl_setopt($curl, CURLOPT_HTTPHEADER,
          array(
              "Authorization: $wablas_api",
          )
      );
      curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
      curl_setopt($curl, CURLOPT_URL, $wablas_endpoint."/api/send-message");
      curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
      curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

      $result = curl_exec($curl);
      curl_close($curl);
      $getdata = json_decode($result);
      if ($getdata->status=='pending') {
          return 'Success';
      } else {
          return 'Fail';
      }
   }

    private function waSend($phone, $name, $invoice, $pos_url, $hours, $left)
   {
    $phone = str_replace('+', '', $phone);
        $check = substr($phone, 0, 1);
        if ($check == '0' || $check == 0) {
            $phone = preg_replace('/^0/', '62', $phone);
        }
       $message = "Halo *".$name."*!

Sudah *".$hours." jam* nih kamu belum melakukan pembayaran untuk Invoice *#".$invoice."*, batas waktu pembayaranmu tersisa *".$left." jam lagi*.

Stoknya sangat terbatas loh, yuk segera lakukan pembayaran pada link berikut, klik:
".$pos_url."

Terimakasih
------------------------
Balas pesan ini jika butuh bantuan atau link tidak bisa diklik :)";
      $wablas_endpoint = DB::table('web_configs')->select('config_value')
      ->where('config_name', 'wablas_endpoint')->first()->config_value;
      $wablas_api = DB::table('web_configs')->select('config_value')
      ->where('config_name', 'wablas_api')->first()->config_value;

        $curl = curl_init();
        $data = [
            'phone' => $phone,
            'message' => $message,
        ];
        curl_setopt($curl, CURLOPT_HTTPHEADER,
            array(
                "Authorization: $wablas_api",
            )
        );
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($curl, CURLOPT_URL, $wablas_endpoint."/api/send-message");
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

        $result = curl_exec($curl);
        curl_close($curl);
        $getdata = json_decode($result);
        if ($getdata->status=='pending') {
            return 'Success';
        } else {
            return 'Fail';
        }
   }

    private function getMidtransStatus($invoice)
    {
        $curl = curl_init();
        $server_key = base64_encode(env('MIDTRANS_SERVER_KEY'));
        $url = "https://api.midtrans.com/v2/".$invoice."/status";
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Accept: application/json",
                "Content-Type: application/json",
                "Authorization: Basic ".$server_key."",
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            return '400';
        } else {
            return json_decode($response, true);
        }
    }

   public function checkConfirmation()
   {
      $confirmation = DB::table('confirmations')->select('id')->where('cf_status', '=', '0')->count('id');
      if (!empty($confirmation)) {
        $r['status'] = '200';
        $r['total'] = $confirmation;
      } else {
        $r['status'] = '400';
      }
      return json_encode($r);
   }

   public function checkPaid()
   {
      $paid = DB::table('pos_transactions')->select('id')->where('pos_status', '=', 'PAID')->count('id');
      if (!empty($paid)) {
        $r['status'] = '200';
        $r['total'] = $paid;
      } else {
        $r['status'] = '400';
      }
      return json_encode($r);
   }

   public function printPaid(Request $request)
   {
      $id = $request->post('id');
      $cust_id = DB::table('pos_transactions')->select('cust_id')->where('id', '=', $id)->get()->first();
      if (!empty($cust_id)) {
        DB::table('customers')->where('id', '=', $cust_id->cust_id)->update([
          'cust_first' => '0',
          'cust_second' => '0',
          'cust_third' => '0'
        ]);
      }
      $update = DB::table('pos_transactions')->where('id', '=', $id)->update([
        'pos_status' => 'SHIPPING NUMBER',
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
      ]);
      $update = DB::table('pos_transaction_details')->where('pt_id', '=', $id)->update([
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
      ]);
      if (!empty($update)) {
        $r['status'] = '200';
      } else {
        $r['status'] = '400';
      }
      return json_encode($r);
   }
}
