<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class VoteIpAddress extends Model
{
    use HasFactory;
    protected $table = 'vote_ip_addresses';
}
