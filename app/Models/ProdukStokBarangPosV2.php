<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdukStokBarangPosV2 extends Model
{
    use HasFactory;

    public function __construct(array $attributes = [])
{
    parent::__construct($attributes);
}

    protected $table = 'stok_barang';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $casts = [
        'id' => 'integer',
    ];

    protected $fillable = [
        'id_barang',
        'tanggal',
        'jumlah',
        'tipe',
    ];
 
    
 public $timestamps = true; // Set ke false jika tabel tidak memiliki timestamps
}

