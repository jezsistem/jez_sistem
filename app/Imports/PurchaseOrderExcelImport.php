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
    private $rows = 0;
    private $data = [];

    /**
     * @return int
     */
    public function startRow(): int
    {
        return 2;
    }

    /**
     * @return int
     */
    public function getRowCount(): int
    {
        return $this->rows;
    }

    /**
     * @param Collection $row
     */
    public function collection(Collection $row)
    {
        $productStocks = [];
        $sizes = [];

        // Pre-fetch product stocks and sizes
        foreach ($row as $value) {
            $sku = ltrim($value[2]);
            $variantName = ltrim($value[1]);
            $variantDesc = ltrim($value[6]);

            $productStocks[$sku] = ProductStock::where('ps_barcode', $sku)->exists();
            $sizes[$variantName . $variantDesc] = Size::where('sz_name', $variantName)
                ->where('sz_schema', $variantDesc)
                ->first();
        }

        // Process rows
        foreach ($row as $value) {
            $sku = ltrim($value[2]);
            $variantName = ltrim($value[1]);
            $variantDesc = ltrim($value[6]);
            $qty = $value[3];
            $poad_purchase_price = $value[4];

            if (!$productStocks[$sku]) continue;

            $size = $sizes[$variantName . $variantDesc];
            if (!$size) continue;

            $productStock = ProductStock::where('ps_barcode', $sku)
                ->where('sz_id', $size->id)
                ->first();

            if (!$productStock) continue;

            $this->data[] = [
                'p_id' => $productStock->p_id,
                'pst_id' => $productStock->id,
                'poad_qty' => $qty,
                'poad_purchase_price' => $poad_purchase_price,
                'poad_total_price' => $qty * $poad_purchase_price,
            ];
        }

    }

    public function getData(): array
    {
        return $this->data;
    }
}

