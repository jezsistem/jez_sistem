<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengeluaranPosV2 extends Model
{
    use HasFactory;
    public function __construct(ConnectionInterface &$db = null, ValidationInterface $validation = null)
    {
        parent::__construct($db, $validation);
    }


    protected $table              = 'pengeluaran';
    protected $primaryKey         = 'id';
    protected $useAutoIncrement   = true;
    protected $returnType         = 'array';

    protected $allowedFields      = [
        'id_toko',
        'id_kategori_pengeluaran',
        'id_pelanggan',
        'pelanggan',
        'jumlah',
        'foto',
        'tgl',
        'catatan'
    ];

     // Atur jika ingin timestamps (created_at, updated_at) aktif atau tidak
 public $timestamps = true; // Atau false jika tidak ingin menggunakan
    
}
