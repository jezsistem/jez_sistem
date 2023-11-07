<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PosShippingInformation extends Model
{
    use HasFactory;
    protected $table = 'pos_shipping_information';
}
