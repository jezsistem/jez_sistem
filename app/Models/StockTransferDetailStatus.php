<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class StockTransferDetailStatus extends Model
{
    use HasFactory;
    protected $table = 'stock_transfer_detail_statuses';
}
