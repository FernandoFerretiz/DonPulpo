<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Mirror of a branch's pos_orders. Only ever upserted by
 * App\Services\Sync\Handlers\PosOrderEventHandler — never written to
 * from an RMS admin screen.
 */
class PosOrder extends Model
{
    protected $table = 'pos_orders';

    protected $fillable = [
        'uuid', 'branch_id', 'order_number', 'user_id',
        'customer_name', 'table_name', 'order_type',
        'subtotal', 'tax', 'tip', 'total', 'status', 'notes', 'paid_at',
        'cancelled_reason', 'cancelled_by', 'cancelled_at',
    ];

    protected function casts(): array
    {
        return [
            'subtotal'     => 'decimal:2',
            'tax'          => 'decimal:2',
            'tip'          => 'decimal:2',
            'total'        => 'decimal:2',
            'paid_at'      => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PosOrderItem::class, 'pos_order_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(PosPayment::class, 'pos_order_id');
    }
}
