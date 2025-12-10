<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuAccessTemplateDetail extends Model
{
    use HasFactory;

    protected $table = 'menu_access_template_details';

    protected $fillable = [
        'menu_access_template_id',
        'menu_access_id',
        'created_at',
        'updated_at'
    ];

    public function menuAccessTemplate()
    {
        return $this->belongsTo(MenuAccessTemplate::class, 'menu_access_template_id');
    }
}
