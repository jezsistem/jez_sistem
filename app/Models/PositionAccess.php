<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PositionAccess extends Model
{
    use HasFactory;

    protected $table = 'position_access';

    protected $fillable = [
        'position_id',
        'route',
        'action',
    ];

    const ACTION_CREATE = 'create';
    const ACTION_READ = 'read';
    const ACTION_UPDATE = 'update';
    const ACTION_DELETE = 'delete';
}
