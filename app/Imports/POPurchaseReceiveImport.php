<?php

namespace App\Imports;

use App\Models\ProductStock;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class POPurchaseReceiveImport implements ToCollection, WithStartRow
{
    private $rows = 0;


    public function startRow(): int
    {
        return 1;
    }

    public function getRowCount(): int
    {
        return $this->rows;
    }

    public function collection(Collection $collection)
    {
        $data = [];

        foreach ($collection as $item)
        {
            $data[] = $item;
        }

        return $data;
    }
}
