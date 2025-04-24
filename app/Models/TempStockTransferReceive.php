<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempStockTransferReceive extends Model
{
    use HasFactory;

    protected $table = 'temp_stock_transfer_receive';
    protected $fillable = [
        'stfd_id',
        'u_id',
        'stfds_qty'
    ];

    public function stockTransferDetail()
    {
        return $this->belongsTo(StockTransferDetail::class, 'stfd_id');
    }
    
    public function user()
    {
        return $this->belongsTo(User::class, 'u_id');
    }
}
