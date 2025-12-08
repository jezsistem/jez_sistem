<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UserShiftExport implements FromCollection, WithHeadings
{
    protected $user_shifts;

    public function __construct($user_shifts)
    {
        $this->user_shifts = $user_shifts;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->user_shifts;
    }

    /**
    * @return array
    */
    public function headings(): array
    {
        return [
            'Nama',
            'Toko',
            'Tanggal Shift',
            'Jam Mulai Shift',
            'Jam Selesai Shift',
            'Actual Cash',
            'Total Nominal Transaksi',
            'Total Transaksi Tunai',
            'Total Transaksi EDC BCA',
            'Total Transaksi EDC BNI',
            'Total Transaksi EDC BRI',
            'Total Transaksi Transfer BCA',
            'Total Transaksi Transfer BNI',
            'Total Transaksi Transfer BRI',
            'Total Transaksi Qris',
        ];
    }

    /**
    * @param mixed $user
    * @return array
    */
    public function map($user_shift): array
    {
        return [
            $user_shift->u_name,
            $user_shift->st_name,
            $user_shift->date,
            $user_shift->start_time,
            $user_shift->end_time,
            $user_shift->actual_cash,
            $user_shift->total_trx,
            $user_shift->trx_cash,
            $user_shift->trx_edc_bca,
            $user_shift->trx_edc_bni,
            $user_shift->trx_edc_bri,
            $user_shift->trx_transfer_bca,
            $user_shift->trx_transfer_bni,
            $user_shift->trx_transfer_bri,
            $user_shift->trx_qris,
        ];
    }
}
