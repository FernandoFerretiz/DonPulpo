<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PhysicalCount extends Model
{
    protected $fillable = [
        'folio', 'warehouse_id', 'adjustment_id', 'notes',
        'count_date', 'status', 'created_by', 'confirmed_by', 'confirmed_at',
    ];

    protected function casts(): array
    {
        return [
            'count_date'   => 'date',
            'confirmed_at' => 'datetime',
        ];
    }

    const STATUS_OPEN      = 'open';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_CANCELLED = 'cancelled';

    const STATUSES = [self::STATUS_OPEN, self::STATUS_CONFIRMED, self::STATUS_CANCELLED];

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function adjustment(): BelongsTo
    {
        return $this->belongsTo(Adjustment::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function confirmedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PhysicalCountItem::class);
    }

    public static function generateFolio(): string
    {
        $prefix = 'CNT-' . now()->format('Ymd') . '-';
        $last   = static::where('folio', 'like', $prefix . '%')->orderByDesc('id')->first();
        $seq    = $last ? ((int) substr($last->folio, -3)) + 1 : 1;
        return $prefix . str_pad($seq, 3, '0', STR_PAD_LEFT);
    }

    public function getStatusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_OPEN      => 'Abierto',
            self::STATUS_CONFIRMED => 'Confirmado',
            self::STATUS_CANCELLED => 'Cancelado',
            default                 => $this->status,
        };
    }

    public function isOpen(): bool { return $this->status === self::STATUS_OPEN; }
}
