<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ExternalAssignmentRequest extends Model
{
    protected $table = 'external_assignment_requests';

    protected $fillable = [
        'ea_id',
        'request_by',
        'ear_date_start',
        'ear_time_start',
        'ear_date_end',
        'ear_time_end',
        'ear_locations',
        'ear_cash_advance',
        'ear_status',
        'ear_approved_by',
        'ear_approved_at',
        'ear_hr_checked_by',
        'ear_hr_checked_at',
        'ear_hr_note',
        'ear_finance_by',
        'ear_finance_at',
        'ear_finance_note',
    ];

    /**
     * Relasi ke tipe assignment
     */
    public function type()
    {
        return $this->belongsTo(ExternalAssignmentType::class, 'ea_id');
    }

    public function getAssignmentRequestsByFilters(
        $startDate = null,
        $endDate = null,
        $userId = null,
        $status = null,
        $typeId = null
    ) {
        $query = DB::table('external_assignment_requests as ear')
            ->select([
                'ear.*',
                'users.u_name',
                'users.u_nip',
                'user_divisions.ud_name',
                'eat.ea_name as type_name',
                'approvers.u_name as approver_name'
            ])
            ->leftJoin('users', 'users.id', '=', 'ear.request_by')
            ->leftJoin('user_divisions', 'user_divisions.id', '=', 'users.ud_id')
            ->leftJoin('external_assignment_types as eat', 'eat.id', '=', 'ear.ea_id')
            ->leftJoin('users as approvers', 'approvers.id', '=', 'ear.ear_approved_by')
            ->orderBy('ear.created_at', 'desc');

        // filter tanggal start
        if ($startDate) {
            $query->where('ear.ear_date_start', '>=', $startDate);
        }

        // filter tanggal end
        if ($endDate) {
            $query->where('ear.ear_date_start', '<=', $endDate);
        }

        // filter user (request_by)
        if ($userId) {
            $query->where('ear.request_by', $userId);
        }

        // filter status
        if ($status) {
            $query->where('ear.ear_status', $status);
        }

        // filter tipe assignment
        if ($typeId) {
            $query->where('ear.ea_id', $typeId);
        }

        return $query->get();
    }


    /**
     * Relasi ke user yang request
     */
    public function requester()
    {
        return $this->belongsTo(User::class, 'request_by');
    }

    /**
     * Relasi ke user yang approve
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'ear_approved_by');
    }

    /**
     * Relasi ke user HR check
     */
    public function hrChecker()
    {
        return $this->belongsTo(User::class, 'ear_hr_checked_by');
    }

    /**
     * Relasi ke user finance
     */
    public function financeProcessor()
    {
        return $this->belongsTo(User::class, 'ear_finance_by');
    }

    /**
     * Relasi ke rundown details
     */
    public function rundowns()
    {
        return $this->hasMany(ExternalAssignmentRequestDetail::class, 'ear_id');
    }

    /**
     * Relasi ke cash advance details
     */
    public function cashDetails()
    {
        return $this->hasMany(ExternalAssignmentRequestCashDetail::class, 'ear_id');
    }

    public function reports()
    {
        return $this->hasMany(ExternalAssignmentRequestReport::class, 'ear_id');
    }
}
