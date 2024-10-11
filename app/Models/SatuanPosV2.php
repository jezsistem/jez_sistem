<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SatuanPosV2 extends Model
{
    use HasFactory;
    // Nama tabel di database
    protected $table = 'satuan';
    
    // Primary key tabel
    protected $primaryKey = 'id';
    
    // Menggunakan auto increment untuk primary key
    public $incrementing = true;

    // Tipe data primary key (default adalah integer)
    protected $keyType = 'int';

    // Kolom yang diizinkan untuk diisi secara massal
    protected $fillable = [
        'id_toko',
        'nama_satuan',
        'status',
    ];

    // Default, return sebagai array
    protected $casts = [
        'id' => 'integer',
        'id_toko' => 'integer',
        'status' => 'boolean',
    ];
}
