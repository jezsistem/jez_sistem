<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriPosV2 extends Model
{

    // Nama tabel di database
    protected $table = 'kategori';

    // Primary key (ID)
    protected $primaryKey = 'id';

    // Apakah menggunakan auto increment pada primary key
    public $incrementing = true;

    // Tipe data dari primary key
    protected $keyType = 'int';

    // Apakah timestamps (`created_at` dan `updated_at`) digunakan
    public $timestamps = false;

    // Kolom yang bisa diisi (mass-assignable)
    protected $fillable = [
        'id_toko',
        'nama_kategori',
        'status'
    ];

    // Tipe data yang dikembalikan oleh model (default adalah `array`)
    protected $casts = [
        'id_toko' => 'integer',
        'nama_kategori' => 'string',
        'status' => 'integer',
    ];
}



