<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    use HasFactory;

    protected $table = 'product_images';

    protected $fillable = [
        'p_id',
        'u_id',
        'file_name',
        'file_path',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'p_id');
    }
}
