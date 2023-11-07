<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ExceptionLocation extends Model
{
    use HasFactory;
    protected $table = 'exception_locations';
}
