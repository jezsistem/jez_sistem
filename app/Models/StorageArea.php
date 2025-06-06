<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StorageArea extends Model
{
    use HasFactory;

    protected $table = 'storage_areas';

    protected $fillable = [
        'st_id',
        'name',
        'description',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class, 'st_id');
    }
}
