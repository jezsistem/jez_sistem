<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuAccessTemplate extends Model
{
    use HasFactory;

    protected $table = 'menu_access_templates';

    protected $fillable = [
        'division_id',
        'template_name',
        'description',
    ];
}
