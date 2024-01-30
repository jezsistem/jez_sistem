<?php

namespace App\Exports;

use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CustomerExport implements FromCollection, WithHeadings
{

    public function headings(): array
    {
        return ['NO','TYPE','STORE', 'PHONE','NAME', 'EMAIL','ADDRESS', 'SHOPPING', 'JOIN', 'INTERVAL JOIN DATE'];
    }
//kini terasa suntuk semakin engkau jauh
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $export = array();

        // get data from database customer
        $customers = Customer::select('customers.*', 'customer_types.ct_name as customer_type_name',
            DB::raw('count(ts_pos_transactions.id) as cust_shopping'))
            ->leftJoin('pos_transactions', 'pos_transactions.cust_id', '=', 'customers.id')
            ->leftJoin('customer_types', 'customer_types.id', '=', 'customers.ct_id')
            ->where('cust_delete', '!=', '1')
            ->groupBy('customers.id')
            ->get();


        foreach ($customers as $key => $customer) {
            $export[$key]['NO'] = $key+1;
            $export[$key]['TYPE'] = $customer->customer_type_name;
            $export[$key]['STORE'] = $customer->cust_store;
            $export[$key]['PHONE'] = $customer->cust_phone;
            $export[$key]['NAME'] = $customer->cust_name;
            $export[$key]['EMAIL'] = $customer->cust_email;
            $export[$key]['PHONE'] = $customer->cust_phone;
            $export[$key]['ADDRESS'] = $this->getAddress($customer);
            $export[$key]['SHOPPING'] = $customer->cust_shopping ?? 0;
            $export[$key]['JOIN'] = $customer->created_at;

            $date = date_create($customer->created_at);
            $now = date_create(date('Y-m-d H:i:s'));
            $diff = date_diff($date, $now);
            $export[$key]['INTERVAL JOIN DATE'] = $diff->format('%y years %m months %d days');
        }
//        return ['NO','TYPE','STORE', 'PHONE','NAME', 'EMAIL', 'ADDRESS', 'SHOPPING', 'JOIN', 'INTERVAL JOIN DATE'];
        return collect($export);
    }

    private function getAddress($data)
    {
        if (!empty($data->cust_province) AND !empty($data->cust_city) AND !empty($data->cust_subdistrict)) {
            $province = DB::table('wilayah')->select('nama')->where('kode', $data->cust_province)->get()->first()->nama;
            $city = DB::table('wilayah')->select('nama')->where('kode', $data->cust_city)->get()->first()->nama;
            $subdistrict = DB::table('wilayah')->select('nama')->where('kode', $data->cust_subdistrict)->get()->first()->nama;
            return $data->cust_address.', '.$subdistrict.', '.$city.', '.$province;
        }

        return $data->cust_address;
    }

}
