<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuTitle extends Model
{
    use HasFactory;

    protected $table = 'menu_titles';

    protected $fillable = [
        'mt_title',
        'mt_sort',
        'created_at',
        'updated_at'
    ];

    public function menuAccesses() {
        return $this->hasMany(MenuAccess::class, 'mt_id', 'id');
    }
}
