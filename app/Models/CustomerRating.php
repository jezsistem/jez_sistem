<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CustomerRating extends Model
{
    use HasFactory;
    protected $table = 'customer_ratings';
}
