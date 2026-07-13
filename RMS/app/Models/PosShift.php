<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PosShift extends Model
{
    // Only ever upserted by App\Services\Sync\Handlers\PosShiftEventHandler.
    protected $fillable = [
        'uuid', 'branch_id', 'user_id', 'terminal_id', 'status',
        'opening_cash', 'expected_cash',
        'counted_cash', 'counted_card', 'counted_transfer',
        'difference', 'opened_at', 'closed_at', 'notes',
    ];

    protected $casts = [
        'opening_cash'     => 'decimal:2',
        'expected_cash'    => 'decimal:2',
        'counted_cash'     => 'decimal:2',
        'counted_card'     => 'decimal:2',
        'counted_transfer' => 'decimal:2',
        'difference'       => 'decimal:2',
        'opened_at'        => 'datetime',
        'closed_at'        => 'datetime',
    ];

    const STATUS_OPEN      = 'open';
    const STATUS_CLOSED    = 'closed';
    const STATUS_CANCELLED = 'cancelled';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pettyCashVouchers(): HasMany
    {
        return $this->hasMany(PettyCashVoucher::class);
    }

    public function isOpen(): bool
    {
        return $this->status === self::STATUS_OPEN;
    }

    public function scopeOpen($query)
    {
        return $query->where('status', self::STATUS_OPEN);
    }
}
