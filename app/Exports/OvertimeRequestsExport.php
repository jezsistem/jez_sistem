<?php

namespace App\Exports;

use App\Models\OvertimeRequest;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class OvertimeRequestsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $filter_status;
    protected $filter_division;
    protected $filter_start_date;
    protected $filter_end_date;
    protected $filter_staff;

    public function __construct($filter_status, $filter_division, $filter_start_date, $filter_end_date, $filter_staff)
    {
        $this->filter_status = $filter_status;
        $this->filter_division = $filter_division;
        $this->filter_start_date = $filter_start_date;
        $this->filter_end_date = $filter_end_date;
        $this->filter_staff = $filter_staff;
    }

    public function collection()
    {
        $query = DB::table('overtime_requests as o')
            ->leftJoin('users as u', 'o.request_by', '=', 'u.id')
            ->leftJoin('user_divisions as d', 'o.ud_id', '=', 'd.id')
            ->leftJoin('overtime_types as ot', 'o.ot_id', '=', 'ot.id')
            ->select(
                'o.id',
                'o.submission_date',
                'd.ud_name as department_name',
                'o.assigned_staff',
                'o.start_date',
                'o.start_time',
                'o.end_date',
                'o.end_time',
                'ot.ot_name as claim',
                'o.status',
                'o.attachment',
                'o.approved_by',
                'o.approved_at',
                'u.u_name as request_by_name',
                'o.created_at',
                'o.attachment',
                'o.report_attachment',
                'o.details',
                'o.report_desc'
            )
            ->orderByDesc('o.id');

        if ($this->filter_status) {
            $query->where('o.status', $this->filter_status);
        }

        if ($this->filter_start_date) {
            $query->whereDate('o.start_date', '>=', $this->filter_start_date);
        }

        if ($this->filter_end_date) {
            $query->whereDate('o.end_date', '<=', $this->filter_end_date);
        }
        if ($this->filter_division) {
            $query->where('o.ud_id', $this->filter_division);
        }
        if ($this->filter_staff) {
            $query->where(function ($q) {
                $q->whereRaw("EXISTS (
                    SELECT 1 FROM ts_users u
                    WHERE JSON_CONTAINS(ts_overtime_requests.assigned_staff, CONCAT('\"', u.id, '\"'))
                    AND u.u_name LIKE ?
                )", ["%{$this->filter_staff}%"]);
            });
        }
        return $query->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Date',
            'Division',
            'Assigned Staff',
            'Start',
            'End',
            'Duration',
            'Claim',
            'Request By',
            'Approved By',
            'Details',
            'Reports',
            'Attachment Description',
            'Attachment Report',
            'Status',
            'Created At',
        ];
    }

    public function map($overtimeRequest): array
    {
        $assignedStaffNames = [];
        $assignedStaff = json_decode($overtimeRequest->assigned_staff, true) ?: [];
        foreach ($assignedStaff as $index => $staffId) {
            $staff = \App\Models\User::find($staffId);
            $assignedStaffNames[] = $staff ? $staff->u_name : 'Unknown';
        }

        $duration = \Carbon\Carbon::parse($overtimeRequest->start_date . ' ' . $overtimeRequest->start_time)
            ->diffInHours(\Carbon\Carbon::parse($overtimeRequest->end_date . ' ' . $overtimeRequest->end_time));

        $attachment = '';
        if ($overtimeRequest->attachment) {
            $attachment = url('/storage/attachments/overtime/' . basename($overtimeRequest->attachment));
        }

        $report_attachment = '';
        if ($overtimeRequest->report_attachment) {
            $report_attachment = url('/storage/overtime_reports/' . basename($overtimeRequest->report_attachment));
        }
        return [
            $overtimeRequest->id,
            \Carbon\Carbon::parse($overtimeRequest->submission_date)->format('d-m-Y'),
            $overtimeRequest->department_name,
            implode(', ', $assignedStaffNames),
            \Carbon\Carbon::parse($overtimeRequest->start_date . ' ' . $overtimeRequest->start_time)->format('d-m-Y H:i'),
            \Carbon\Carbon::parse($overtimeRequest->end_date . ' ' . $overtimeRequest->end_time)->format('d-m-Y H:i'),
            $duration,
            $overtimeRequest->claim,
            $overtimeRequest->request_by_name,
            $overtimeRequest->approved_by,
            $overtimeRequest->details,
            $overtimeRequest->report_desc,
            $attachment,
            $report_attachment,
            $overtimeRequest->status,
            \Carbon\Carbon::parse($overtimeRequest->created_at)->format('d-m-Y H:i'),
        ];
    }
}
