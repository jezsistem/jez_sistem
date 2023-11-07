<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class StockClosingDetail extends Model
{
    use HasFactory;
    protected $table = 'stock_closing_details';
}
