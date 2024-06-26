<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;

class TransactionOnlineImport implements ToCollection, WithStartRow
{
    private $rows = 0;

    public function startRow(): int
    {
        return 2;
    }

    public function getRowCount(): int
    {
        return $this->rows;
    }

    /**
     * @param Collection $collection
     */
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
