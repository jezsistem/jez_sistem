<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BahanBakuPosV2 extends Model
{
    use HasFactory;

    // Specify the table associated with the model if it doesn't follow Laravel's naming convention
    protected $table = 'bahan_baku';

    // Define the primary key if it's not the default 'id'
    protected $primaryKey = 'id';

    // Allow auto-incrementing for the primary key
    public $incrementing = true;

    // Define the data type for the primary key
    protected $keyType = 'int';

    // Allow mass assignment for these fields
    protected $fillable = [
        'id_toko',
        'nama_bahan',
        'harga',
        'status',
    ];

    // Optionally, set timestamps if your table uses created_at and updated_at
    public $timestamps = true;
}
