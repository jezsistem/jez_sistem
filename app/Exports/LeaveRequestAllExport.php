<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LeaveRequestAllExport implements FromCollection, WithHeadings, WithMapping
{

    protected $leaveRequests;

    public function __construct($leaveRequests)
    {
        $this->leaveRequests = $leaveRequests;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->leaveRequests;
    }
    
    public function headings(): array
    {
        return [
            'No.',
            'Nama Pegawai',
            'Divisi',
            'Tanggal Mulai',
            'Waktu Mulai',
            'Tanggal Selesai',
            'Waktu Selesai',
            'Durasi',
            'Jenis Leave',
            'Status',
            'Alasan',
            'Catatan',
            'Disetujui/Ditolak Oleh',
            'Disetujui/Ditolak Pada',
            'Tanggal Request'
        ];
    }

    public function map($item): array
    {
        static $no = 1;

        return [
            $no++,
            $item->u_name ?? '-',
            $item->ud_name ?? '-',
            $item->lr_start_date  ? date('d/m/Y', strtotime($item->lr_start_date)) : '-',
            $item->lr_start_time  ? date('H:i', strtotime($item->lr_start_time)) : '-',
            $item->lr_end_date ? date('d/m/Y', strtotime($item->lr_end_date)) : '-',
            $item->lr_end_time ? date('H:i', strtotime($item->lr_end_time)) : '-',
            ($item->lr_total_days ?? 0) . ' ' . ($item->lr_unit ?? ''),
            $item->lt_name ?? '-',
            ucfirst($item->lr_status ?? ''),
            $item->lr_reason ?? '-',
            $item->lr_admin_notes ?? '-',
            $item->approver_name ?? '-',
            $item->lr_approved_at ? date('d/m/Y H:i', strtotime($item->lr_approved_at)) : '-',
            $item->created_at ? date('d/m/Y H:i', strtotime($item->created_at)) : '-'
        ];
    }
}
