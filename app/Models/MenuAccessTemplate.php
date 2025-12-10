<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuAccessTemplate extends Model
{
    use HasFactory;

    protected $table = 'menu_access_templates';

    protected $fillable = [
        'template_name',
        'division_id',
        'description',
        'created_at',
        'updated_at'
    ];

    public function division()
    {
        return $this->belongsTo(UserDivision::class, 'division_id');
    }

    public function menuAccessTemplateDetails()
    {
        return $this->hasMany(MenuAccessTemplateDetail::class, 'menu_access_template_id');
    }
}
