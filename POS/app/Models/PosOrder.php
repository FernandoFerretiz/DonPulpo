<?php

namespace App\Models;

use App\Concerns\BelongsToCurrentBranch;
use App\Concerns\HasSyncableUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PosOrder extends Model
{
    use HasSyncableUuid;
    use BelongsToCurrentBranch;

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
        'total',
        'status',
        'notes',
        'paid_at',
        'branch_id',
        'sync_status',
        'cancelled_reason',
        'cancelled_by',
        'cancelled_at',
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

    public function cancelledByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }
}
