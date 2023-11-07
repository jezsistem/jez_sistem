<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\WebConfig;
use App\Models\User;
use App\Models\CustomerType;

class CustomerTypeController extends Controller
{
    public function getDatatables(Request $request)
    {
        if(request()->ajax()) {
            return datatables()->of(CustomerType::select('id', 'ct_name', 'ct_description', 'ct_delete', 'created_at', 'updated_at')
            ->where('ct_delete', '!=', '1'))
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhere('ct_name', 'LIKE', "%$search%")
                        ->orWhere('ct_description', 'LIKE', "%$search%");
                    });
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function storeData(Request $request)
    {
        $customer_type = new CustomerType;
        $mode = $request->input('_mode_ct');
        $id = $request->input('_id_ct');

        $data = [
            'ct_name' => strtoupper($request->input('ct_name')),
            'ct_description' => $request->input('ct_description'),
            'ct_delete' => '0',
        ];

        $store = $customer_type->storeData($mode, $id, $data);
        if ($store) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function deleteData(Request $request)
    {
        $customer_type = new CustomerType;
        $id = $request->input('_id');
        $store = $customer_type->deleteData($id);
        if ($store) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function reloadCustomerType()
    {
        $data = [
            'ct_id' => CustomerType::where('ct_delete', '!=', '1')->orderByDesc('id')->pluck('ct_name', 'id'),
		];
        return view('app.customer._reload_customer_type', compact('data'));
    }
}
