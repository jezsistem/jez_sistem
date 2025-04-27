<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ApprovalPOExport implements FromArray, WithHeadings, ShouldAutoSize
{
    protected $data;

    /**
     * Constructor to initialize data.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Return the data array for export.
     *
     * @return array
     */
    public function array(): array
    {
        return $this->data;
    }

    /**
     * Return the headings for the export.
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'No',
            'Tanggal Terima',
            'Invoice',
            'SKU',
            'Brand',
            'Artikel',
            'Warna',
            'Size',
            'Tipe',
            'Qty Terima',
            'Current Stock',
            'Harga Beli',
            'Total',
        ];
    }
}