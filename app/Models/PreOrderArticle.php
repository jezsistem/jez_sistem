<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PreOrderArticle extends Model
{
    use HasFactory;

    protected $table = 'pre_order_articles';

    protected $fillable = [
        'po_id',
        'pr_id',
        'poa_draft',
        'poa_status',
    ];

    public const POA_STATUS_ON_PROGRESS = 1;
    public const POA_STATUS_COMPLETED = 2;
    public const POA_STATUS_CANCELLED = 3;

    public function deleteData($id)
    {
        try {
            $delete = DB::table($this->table)->where('id', $id)->delete();
            if ($delete) {
                return true;
            } else {
                return false;
            }
        } catch (\Illuminate\Database\QueryException $ex) {
            if($ex->getCode() === '23000') {
                return false;
            }
        }
    }

    public static function getStatusOptions()
    {
        return [
            self::POA_STATUS_ON_PROGRESS => 'On Progress',
            self::POA_STATUS_COMPLETED => 'Completed',
            self::POA_STATUS_CANCELLED => 'Cancelled',
        ];
    }
}
