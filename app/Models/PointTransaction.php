<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PointTransaction extends Model
{
    protected $fillable = [
        'user_id',
        'order_id',
        'type',
        'amount',
        'balance_after',
        'description',
        'performed_by',
    ];

    protected $casts = [
        'amount'       => 'integer',
        'balance_after'=> 'integer',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function performedBy()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    // Helper: catat transaksi poin
    public static function record(
        int    $userId,
        string $type,
        int    $amount,
        int    $balanceAfter,
        string $description = null,
        int    $orderId     = null,
        int    $performedBy = null
    ): self {
        return self::create([
            'user_id'      => $userId,
            'order_id'     => $orderId,
            'type'         => $type,
            'amount'       => $amount,
            'balance_after'=> $balanceAfter,
            'description'  => $description,
            'performed_by' => $performedBy,
        ]);
    }
}
