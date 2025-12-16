<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuAccess extends Model
{
    use HasFactory;

    protected $table = 'menu_accesses';

    protected $fillable = [
        'mt_id',
        'ma_title',
        'ma_slug',
        'ma_sort',
        'created_at',
        'updated_at'
    ];

    public function menuTitle() {
        return $this->belongsTo(MenuTitle::class, 'mt_id');
    }
}
