<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdukVarianPosV2 extends Model
{
    use HasFactory;

    public function __construct(array $attributes = [])
{
    parent::__construct($attributes);
}

    protected $table = 'varian';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    // Daftar kolom yang bisa diisi secara massal
    protected $fillable = [
        'id_barang',
        'id_satuan',
        'nama_varian',
        'harga_jual',
        'harga_modal',
        'keterangan',
        'kelola_stok',
        'stok',
        'stok_min',
        'status',
    ];

    // Mengubah tipe data untuk beberapa kolom jika diperlukan
    protected $casts = [
        'id' => 'integer',
        'id_barang' => 'integer',
        'id_satuan' => 'integer',
        'harga_jual' => 'decimal:2',
        'harga_modal' => 'decimal:2',
        'stok' => 'integer',
        'stok_min' => 'integer',
    ];
}
