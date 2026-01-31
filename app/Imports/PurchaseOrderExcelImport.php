<?php

namespace App\Imports;

use App\Models\ProductStock;
use App\Models\Size;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;

class PurchaseOrderExcelImport implements ToCollection, WithStartRow
{
    private $rows = 0; // Awalnya nol
    private $data = [];

    /**
     * Mulai pembacaan dari baris ke-2 (karena baris pertama adalah header)
     */
    public function startRow(): int
    {
        return 2;
    }

    /**
     * Mengembalikan jumlah baris yang berhasil diproses
     */
    public function getRowCount(): int
    {
        return $this->rows;
    }

    /**
     * Mengembalikan data hasil parsing
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Fungsi utama import excel
     */
    public function collection(Collection $rows)
    {
        $productStocks = [];
        $sizes = [];

        // Prefetch: Mengambil dulu semua size & stok yang mungkin digunakan
        foreach ($rows as $value) {
            $sku = ltrim($value[0]); // barcode
//            $variantName = ltrim($value[1]); // size name
//            $variantDesc = ltrim($value[6]); // size schema

            if (!isset($productStocks[$sku])) {
                $productStocks[$sku] = ProductStock::where('ps_barcode', $sku)->exists();
            }

//            $sizeKey = $variantName . $variantDesc;
//            if (!isset($sizes[$sizeKey])) {
//                $sizes[$sizeKey] = Size::where('sz_name', $variantName)
//                    ->where('sz_schema', $variantDesc)
//                    ->first();
//            }
        }

        // Proses setiap baris
        foreach ($rows as $value) {
            $sku = ltrim($value[0]);
            $disc = ltrim($value[1]);
            $ex_disc = ltrim($value[2]);
            $sub_disc = ltrim($value[3]);
            $qty = ltrim($value[4]);
            $purchase_price = ltrim($value[5]);

            if (!$productStocks[$sku]) {
                $this->data[] = [
                    'sku' => $sku,
                    'status' => 'Not Found',
                    'disc' => $disc,
                    'ex_disc' => $ex_disc,
                    'sub_disc' => $sub_disc,
                    'poad_qty' => $qty,
                    'purchase_price' => $purchase_price
                ];
                $this->rows++;
                continue;
            }

            $productStock = ProductStock::where('ps_barcode', $sku)->first();

            //calc discount if purchase price is not zero

            if ($purchase_price != 0) {
                $disc = ($purchase_price/$productStock->ps_price_tag) * 100;

                $disc = 100 - $disc;
            }

            if (!$productStock) {
                $this->data[] = [
                    'sku' => $sku,
                    'status' => 'Not Found',
                    'disc' => $disc,
                    'ex_disc' => $ex_disc,
                    'sub_disc' => $sub_disc,
                    'poad_qty' => $qty,
                    'purchase_price' => $purchase_price
                ];
                $this->rows++;
                continue;
            }

            $this->data[] = [
                'p_id' => $productStock->p_id,
                'pst_id' => $productStock->id,
                'disc' => $disc,
                'ex_disc' => $ex_disc,
                'sub_disc' => $sub_disc,
                'poad_qty' => $qty,
                'sku' => $sku,
                'status' => 'Found',
                'purchase_price' => $purchase_price
            ];

            $this->rows++;
        }
    }
}