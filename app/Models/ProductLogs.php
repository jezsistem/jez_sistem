<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductLogs extends Model
{
    use HasFactory;

    protected $table = 'product_logs';
    protected $fillable = [
        'p_id',
        'pst_id',
        'user_id',
        'levels',
        'target_column',
        'source',
        'data_before',
        'data_after',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'p_id');
    }

    public function productStock()
    {
        return $this->belongsTo(ProductStock::class, 'pst_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function storeProductLog($pId, $userId, $levels, $targetColumn, $source, $dataBefore, $dataAfter, $timestamp)
    {
        return self::create([
            'p_id' => $pId,
            'user_id' => $userId,
            'levels' => $levels,
            'target_column' => $targetColumn,
            'source' => $source,
            'data_before' => $dataBefore,
            'data_after' => $dataAfter,
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ]);
    }

    public function storeProductStockLog($pstId, $pId, $userId, $levels, $targetColumn, $source, $dataBefore, $dataAfter, $timestamp)
    {
        return self::create([
            'pst_id' => $pstId,
            'p_id' => $pId,
            'user_id' => $userId,
            'levels' => $levels,
            'target_column' => $targetColumn,
            'source' => $source,
            'data_before' => $dataBefore,
            'data_after' => $dataAfter,
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ]);
    }

    public const LEVEL_ARTICLE = 'article';
    public const LEVEL_SKU = 'sku';
}
