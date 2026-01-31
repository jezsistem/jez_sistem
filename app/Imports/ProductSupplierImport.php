<?php

namespace App\Imports;

use App\Models\ProductSupplier;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductSupplierImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $name = isset($row['nama']) ? trim($row['nama']) : null;

        if (!$name) {
            return null;
        }

        return ProductSupplier::updateOrCreate(
            ['ps_name' => $name],
            [
                'ps_email'       => isset($row['email']) ? trim($row['email']) : null,
                'ps_phone'       => isset($row['telepon']) ? trim($row['telepon']) : null,
                'ps_address'     => isset($row['alamat']) ? trim($row['alamat']) : null,
                'ps_description' => isset($row['deskripsi']) ? trim($row['deskripsi']) : null,
                'ps_rekening'    => isset($row['rekening']) ? trim($row['rekening']) : null,
                'ps_npwp'        => isset($row['npwp']) ? trim($row['npwp']) : null,
                'ps_pkp'         => (isset($row['pkp']) && strtoupper(trim($row['pkp'])) === 'YA') ? '1' : '0',
                'ps_delete'      => '0',
            ]
        );
    }
}
