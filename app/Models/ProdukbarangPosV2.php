<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdukbarangPosV2 extends Model
{
    use HasFactory;

    public function __construct(array $attributes = [])
{
    parent::__construct($attributes);
}

    protected $table = 'barang';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

       protected $casts = [
        'id' => 'integer',
    ];

    // Daftar kolom yang bisa diisi secara massal
    protected $fillable = [
        'id_toko',
        'id_kategori',
        'nama_barang',
        'harga_jual',
        'harga_modal',
        'status',
        'kelola_stok',
        'stok',
        'stok_min',
        'foto',
        'barcode',
    ];
 // Atur jika ingin timestamps (created_at, updated_at) aktif atau tidak
 public $timestamps = true; // Atau false jika tidak ingin menggunakan
}
