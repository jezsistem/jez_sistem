<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ProductLocationSetupTransaction extends Model
{
    use HasFactory;
    protected $table = 'product_location_setup_transactions';
}
