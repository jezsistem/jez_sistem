<?php

namespace App\Http\Controllers;
use App\Imports\POPurchaseReceiveImport;
use App\Models\PurchaseOrderReceiveImportExcel;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet;

class POPurchaseReceiveImportController extends Controller
{
    public function importExcel(Request $request)
    {

        try {

            if ($request->hasFile('importFile')) {

                $file = $request->file('importFile');
                // membuat nama file unik
                $nama_file = rand().$file->getClientOriginalName();

                // upload ke folder file_siswa di dalam folder public
                $file->move('excel',$nama_file);

                $import = new POPurchaseReceiveImport;
                $data = Excel::toArray($import, public_path('excel/'.$nama_file));

                if ($import->getRowCount() >= 0) {
                    $processData = $this->processImportData($data[0]);

                    PurchaseOrderReceiveImportExcel::insert($processData);

                    $r['data'] = $processData;
                    $r['status'] = '200';
                } else {
                    $r['status'] = '419';
                }
            }  else {
                $r['status'] = '400';
            }

//            delete file
            unlink(public_path('excel/'.$nama_file));
                
            return json_encode($r);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    private function processImportData($data)
    {
        $processedData = [];

        foreach ($data as $item) {
            $barcodeAndQty = $item[0];

            $explodedData = explode(',', $barcodeAndQty);

            if (count($explodedData) == 1) {
                $explodedData[1] = 1;
            }

            $barcode = $explodedData[0];
            $qty = $explodedData[1];

            // Check if barcode already exists in processedData
            $existingKey = array_search($barcode, array_column($processedData, 'barcode'));

            if ($existingKey !== false) {
                // If barcode exists, add the quantity to the existing entry
                $processedData[$existingKey]['qty'] += $qty;
            } else {
                // If barcode doesn't exist, create a new entry
                $rowData = [
                    'barcode' => $barcode,
                    'qty' => $qty,
                ];
                $processedData[] = $rowData;
            }
        }

        return $processedData;
    }

}
