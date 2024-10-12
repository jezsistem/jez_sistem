<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdukKelolastokPosV2 extends Model
{
    use HasFactory;
    public function __construct(array $attributes = [])
{
    parent::__construct($attributes);
}

    protected $table = 'kelola_stok';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

   
    protected $casts = [
        'id' => 'integer',
    ];

     protected $fillable = [
        'id_varian',
        'tanggal',
        'jumlah',
        'tipe',
    ];

    // Menggunakan timestamps created_at dan updated_at secara otomatis
    public $timestamps = true; // Set ke false jika tidak ada timestamps
}

