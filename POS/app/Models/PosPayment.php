<?php

namespace App\Models;

use App\Concerns\BelongsToCurrentBranch;
use App\Concerns\HasSyncableUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PosPayment extends Model
{
    use HasSyncableUuid;
    use BelongsToCurrentBranch;

    protected $table = 'pos_payments';

    protected $fillable = [
        'pos_order_id',
        'user_id',
        'method',
        'amount',
        'change_amount',
        'status',
        'paid_at',
        'branch_id',
        'sync_status',
    ];

    protected function casts(): array
    {
        return [
            'amount'        => 'decimal:2',
            'change_amount' => 'decimal:2',
            'paid_at'       => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(PosOrder::class, 'pos_order_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
