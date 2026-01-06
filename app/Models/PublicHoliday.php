<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PublicHoliday extends Model
{
    protected $table = 'public_holidays';

    protected $fillable = [
        'holiday_date',
        'description',
        'year',
        'is_national',
        'source',
    ];
}