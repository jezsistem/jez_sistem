<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UpcloudBalanceController extends Controller
{
    public function getBalance()
    {
        $curl = curl_init();
		curl_setopt_array($curl, array(
		CURLOPT_URL => "https://api.upcloud.com/1.2/account",
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 30,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "GET",
		CURLOPT_HTTPHEADER => array(
			'Content-Type:application/json',
            'Authorization: Basic '. base64_encode("zonakarya2:Jez0808!")
		),
		));
		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);
		if ($err) {
			return "cURL Error #:" . $err;
		} else {
			$data = json_decode($response, true);
            $exp = explode('.', $data['account']['credits']);
            $balace = $exp[0]/100;
            $r['balance'] = $balace;
            $r['status'] = '200';
            return json_encode($r);
		}
    }
}
