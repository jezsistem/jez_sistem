<?php

namespace App\Http\Controllers;

use App\Models\StoreType;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class SendNotificationController extends Controller
{
    public function sendNotification(Request $request)
    {
        $client = new Client();

        $division = strtoupper($request->division);
        $mode = $request->mode;

        if ($mode == 'penerimaan') {
            $no_order = $request->no_order;
            $divisi_direct = StoreType::where('stt_name', $division)->first()->stt_description;
            $noHp = $divisi_direct;
            $pesan = "[Testing Notification Penerimaan], \n\nLogistik telah melakukan Penerimaan pada nomor Purchase Order . $no_order. \nHarap segera melakukan cross cek pada nomor order tersebut.";
        }

        try {
            $response = $client->get('http://jezdb.com:3001/api', [
                'query' => [
                    'nohp' => $noHp,
                    'pesan' => $pesan,
                ]
            ]);

            if ($response->getStatusCode() == 200) {
                $responseData = json_decode($response->getBody()->getContents(), true);
                $r['status'] = '200';
            }

        } catch (\Exception $e) {
            $r['status'] = '500';
            $r['message'] = 'Error communicating with external API';
        }

    }
}
