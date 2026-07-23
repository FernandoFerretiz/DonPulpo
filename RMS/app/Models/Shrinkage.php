<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shrinkage extends Model
{
    protected $fillable = ['folio', 'warehouse_id', 'reason', 'notes', 'shrinkage_date', 'status', 'created_by'];

    protected function casts(): array
    {
        return ['shrinkage_date' => 'date'];
    }

    const REASON_EXPIRED              = 'expired';
    const REASON_DAMAGED              = 'damaged';
    const REASON_SPILLAGE             = 'spillage';
    const REASON_INTERNAL_CONSUMPTION = 'internal_consumption';
    const REASON_OTHER                = 'other';

    const REASONS = [
        self::REASON_EXPIRED, self::REASON_DAMAGED, self::REASON_SPILLAGE,
        self::REASON_INTERNAL_CONSUMPTION, self::REASON_OTHER,
    ];

    const STATUS_DRAFT     = 'draft';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    const STATUSES = [self::STATUS_DRAFT, self::STATUS_COMPLETED, self::STATUS_CANCELLED];

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ShrinkageItem::class);
    }

    public static function generateFolio(): string
    {
        $prefix = 'MER-' . now()->format('Ymd') . '-';
        $last   = static::where('folio', 'like', $prefix . '%')->orderByDesc('id')->first();
        $seq    = $last ? ((int) substr($last->folio, -3)) + 1 : 1;
        return $prefix . str_pad($seq, 3, '0', STR_PAD_LEFT);
    }

    public function getReasonLabel(): string
    {
        return match ($this->reason) {
            self::REASON_EXPIRED              => 'Caducidad',
            self::REASON_DAMAGED              => 'Daño',
            self::REASON_SPILLAGE             => 'Derrame',
            self::REASON_INTERNAL_CONSUMPTION => 'Consumo interno',
            self::REASON_OTHER                => 'Otro',
            default                            => $this->reason,
        };
    }

    public function getStatusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT     => 'Borrador',
            self::STATUS_COMPLETED => 'Completada',
            self::STATUS_CANCELLED => 'Cancelada',
            default                => $this->status,
        };
    }

    public function isDraft(): bool { return $this->status === self::STATUS_DRAFT; }
}
