<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModalLockAllowedModel extends Model
{
    use HasFactory;
    protected $table = 'modal_lock_allowed_models';

    protected $fillable = [
        'model_type',
        'identifier',
        'is_active',
    ];
}
