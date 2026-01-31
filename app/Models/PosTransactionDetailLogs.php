<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PosTransactionDetailLogs extends Model
{
    use HasFactory;
    protected $table = 'pos_transaction_detail_logs';

    protected $fillable = [
        'ptd_id',
        'ps_purchase_price',
        'ps_price_tag',
        'ps_sell_price',
        'p_turnoverclass',
    ];

    public function posTransactionDetail()
    {
        return $this->belongsTo(PosTransactionDetail::class, 'ptd_id', 'id');
    }
}
