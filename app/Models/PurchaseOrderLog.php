<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderLog extends Model
{
    use HasFactory;

    protected $table = 'purchase_order_logs';

    protected $fillable = [
        'po_id',
        'user_id',
        'type',
        'target_column',
        'target_item',
        'before',
        'after',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'po_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function storePOLog($poId, $userId, $type, $target, $before, $after, $timestamp)
    {
        return self::create([
            'po_id' => $poId,
            'user_id' => $userId,
            'type' => $type,
            'target_column' => $target,
            'before' => $before,
            'after' => $after,
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ]);
    }

    public function storePOItemLog($poId, $userId, $type,$target_column, $target_item, $before, $after, $timestamp)
    {
        return self::create([
            'po_id' => $poId,
            'user_id' => $userId,
            'type' => $type,
            'target_column' => $target_column,
            'target_item' => $target_item,
            'before' => $before,
            'after' => $after,
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ]);
    }

    public const TYPE_PURCHASE_ORDER = 'purchase_order';
    public const TYPE_ARTICLE = 'article';
    public const TYPE_ITEMS = 'items';
}
