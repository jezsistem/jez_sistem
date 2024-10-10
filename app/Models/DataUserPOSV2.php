<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class DataUserPOSV2 extends Model
{
    // Table associated with the model
    protected $table = 'user';

    // Primary key for the model
    protected $primaryKey = 'id';

    // Indicates if the IDs are auto-incrementing
    public $incrementing = true;

    // Indicates whether the model should be timestamped
    public $timestamps = false; // Set this to true if your table has created_at and updated_at columns

    // Mass assignable attributes
    protected $fillable = [
        'id_toko',
        'nama',
        'email',
        'nohp',
        'status',
        'password',
        'reward',
    ];

    // Optional: Define relationships if needed
    // public function someRelationship() {
    //     return $this->hasMany(SomeOtherModel::class);
    // }
}
