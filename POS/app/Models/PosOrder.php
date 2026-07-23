<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PosOrder extends Model
{
    protected $table = 'pos_orders';

    protected $fillable = [
        'order_number',
        'user_id',
        'customer_name',
        'table_name',
        'order_type',
        'subtotal',
        'tax',
        'tip',
        'discount_code',
        'discount_percent',
        'discount_amount',
        'total',
        'status',
        'notes',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'subtotal'         => 'decimal:2',
            'tax'              => 'decimal:2',
            'tip'              => 'decimal:2',
            'discount_percent' => 'decimal:2',
            'discount_amount'  => 'decimal:2',
            'total'            => 'decimal:2',
            'paid_at'          => 'datetime',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(PosOrderItem::class, 'pos_order_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(PosPayment::class, 'pos_order_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
