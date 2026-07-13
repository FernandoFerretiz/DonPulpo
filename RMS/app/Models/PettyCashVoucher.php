<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PettyCashVoucher extends Model
{
    // Only ever upserted by App\Services\Sync\Handlers\PettyCashVoucherEventHandler.
    protected $fillable = [
        'uuid', 'branch_id',
        'folio', 'requested_by', 'authorized_by', 'rejected_by', 'paid_by',
        'pos_shift_id', 'petty_cash_category_id',
        'beneficiary', 'concept', 'amount', 'status',
        'requested_at', 'authorized_at', 'rejected_at', 'paid_at', 'cancelled_at',
        'rejection_reason', 'notes',
    ];

    protected $casts = [
        'amount'        => 'decimal:2',
        'requested_at'  => 'datetime',
        'authorized_at' => 'datetime',
        'rejected_at'   => 'datetime',
        'paid_at'       => 'datetime',
        'cancelled_at'  => 'datetime',
    ];

    const STATUS_PENDING    = 'pending';
    const STATUS_AUTHORIZED = 'authorized';
    const STATUS_REJECTED   = 'rejected';
    const STATUS_PAID       = 'paid';
    const STATUS_CANCELLED  = 'cancelled';

    const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_AUTHORIZED,
        self::STATUS_REJECTED,
        self::STATUS_PAID,
        self::STATUS_CANCELLED,
    ];

    public static function generateFolio(): string
    {
        $prefix = 'VC-' . now()->format('Ymd') . '-';
        $last   = static::where('folio', 'like', $prefix . '%')->orderByDesc('id')->first();
        $seq    = $last ? ((int) substr($last->folio, -3)) + 1 : 1;
        return $prefix . str_pad($seq, 3, '0', STR_PAD_LEFT);
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function authorizedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'authorized_by');
    }

    public function rejectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function paidBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(PosShift::class, 'pos_shift_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(PettyCashCategory::class, 'petty_cash_category_id');
    }

    public function getStatusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING    => 'Pendiente',
            self::STATUS_AUTHORIZED => 'Autorizado',
            self::STATUS_REJECTED   => 'Rechazado',
            self::STATUS_PAID       => 'Pagado',
            self::STATUS_CANCELLED  => 'Cancelado',
            default                 => $this->status,
        };
    }

    public function isPending(): bool    { return $this->status === self::STATUS_PENDING; }
    public function isAuthorized(): bool { return $this->status === self::STATUS_AUTHORIZED; }
    public function isPaid(): bool       { return $this->status === self::STATUS_PAID; }
}
