<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModalLockConfig extends Model
{
    use HasFactory;

    protected $table = 'modal_lock_configs';

    protected $fillable = [
        'name',
        'value',
        'description',
    ];
    
}
