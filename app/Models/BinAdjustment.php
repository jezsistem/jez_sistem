<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BinAdjustment extends Model
{
    use HasFactory;
    protected $table = 'bin_adjustments';
    protected $fillable = [
        'pls_id',
        'pst_id',
        'u_id',
        'ba_code',
        'ba_cogs',
        'ba_old_qty',
        'ba_new_qty',
        'ba_adjust',
        'ba_adjust_type',
        'ba_note',
        'created_at',
        'ba_status',
        'ba_approve',
        'ba_executor',
        'approved_at',
        'execute_at',
    ];

    const UNKNOWN = 0;
    const NEED_APPROVAL = 1;
    const NEED_EXECUTION = 2;
    const CANCEL = 3;
    const DONE = 4;
    const REJECTED = 5;

    public static function getStatusOptions()
    {
        return [
            self::UNKNOWN => 'Unknown',
            self::NEED_APPROVAL => 'Need Approval',
            self::NEED_EXECUTION => 'Need Execution',
            self::CANCEL => 'Cancel',
            self::DONE => 'Done',
            self::REJECTED => 'Rejected',
        ];
    }
}
