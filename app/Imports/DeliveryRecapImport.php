<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class DeliveryRecapImport implements ToCollection
{
    public $resis = [];

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            // Lewati header jika baris pertama berisi teks “resi”
            if ($index === 0 && strtolower(trim($row[0])) === 'resi') {
                continue;
            }

            // Simpan setiap nomor resi (pastikan tidak kosong)
            if (!empty($row[0])) {
                $this->resis[] = trim($row[0]);
            }
        }
    }
}