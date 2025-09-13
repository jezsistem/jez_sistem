<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OnlineTransactionChat extends Model
{
    use HasFactory;

    protected $table = 'online_transaction_chat_history';

    protected $fillable = [
        'ot_id',
        'user_id',
        'is_amp',
        'is_readed',
        'messages',
    ];
}
