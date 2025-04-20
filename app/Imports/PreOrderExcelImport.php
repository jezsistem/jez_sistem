<?php

namespace App\Imports;

use App\Models\PreOrderArticle;
use App\Models\PreOrderArticleDetails;
use App\Models\ProductStock;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;

class PreOrderExcelImport implements ToCollection, WithStartRow
{
    private $rows = 0;
    private $data = [];
    private $po_id; // Add a property to store the po_id

    public function __construct($po_id)
    {
        $this->po_id = $po_id; // Initialize po_id in the constructor
    }

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
        foreach ($row as $value) {
            $sku = ltrim($value[0]); // Assuming SKU is in the first column
            $qty = $value[1]; // Assuming quantity is in the second column

            $productStock = ProductStock::where('ps_barcode', $sku)->first();

            if (!$productStock) {
                continue;
            }

            $poid = $this->po_id;
            $prid = $productStock->p_id;
            $psid = $productStock->id;
            $price = $productStock->ps_price_tag; // Assuming price is stored in ps_price

            // Check if pre_order_article exists
            $check_poa = PreOrderArticle::where(['po_id' => $poid, 'pr_id' => $prid])->exists();

            if (!$check_poa) {
                $poa_id = PreOrderArticle::insertGetId([
                    'po_id' => $poid,
                    'pr_id' => $prid,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $poa_id = PreOrderArticle::where(['po_id' => $poid, 'pr_id' => $prid])
                    ->value('id');
            }

            // Check if pre_order_article_details exists
            $check_poad = PreOrderArticleDetails::where(['poa_id' => $poa_id, 'pst_id' => $psid])->exists();

            $total_price = $qty * $price; // Calculate total price based on quantity and price

            if (!$check_poad) {
                PreOrderArticleDetails::insert([
                    'poa_id' => $poa_id,
                    'pst_id' => $psid,
                    'poad_qty' => $qty,
                    'poad_total_price' => $total_price, // Add total price
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                // Update the quantity and total price if it already exists
                PreOrderArticleDetails::where(['poa_id' => $poa_id, 'pst_id' => $psid])
                    ->increment('poad_qty', $qty);

                PreOrderArticleDetails::where(['poa_id' => $poa_id, 'pst_id' => $psid])
                    ->increment('poad_total_price', $total_price);
            }
        }
    }

    public function getData(): array
    {
        return $this->data;
    }
}
