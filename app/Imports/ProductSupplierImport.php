<?php

namespace App\Imports;

use App\Models\ProductSupplier;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductSupplierImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        if ($row['nama'] == null) {
            return null;
        }
        return new ProductSupplier([
            'ps_name' => ltrim($row['nama']),
            'ps_email' => ltrim($row['email']),
            'ps_phone' => ltrim($row['telepon']),
            'ps_address' => ltrim($row['alamat']),
            'ps_description' => ltrim($row['deskripsi']),
            'ps_rekening' => ltrim($row['rekening']),
            'ps_npwp' => ltrim($row['npwp']),
            'ps_pkp' => ($row['pkp'] == 'YA') ? '1' : '0',
            'ps_delete' => '0',
        ]);
    }
}
