<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Adjustment extends Model
{
    protected $fillable = ['folio', 'warehouse_id', 'reason', 'notes', 'adjustment_date', 'status', 'created_by'];

    protected function casts(): array
    {
        return ['adjustment_date' => 'date'];
    }

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
        return $this->hasMany(AdjustmentItem::class);
    }

    public static function generateFolio(): string
    {
        $prefix = 'AJU-' . now()->format('Ymd') . '-';
        $last   = static::where('folio', 'like', $prefix . '%')->orderByDesc('id')->first();
        $seq    = $last ? ((int) substr($last->folio, -3)) + 1 : 1;
        return $prefix . str_pad($seq, 3, '0', STR_PAD_LEFT);
    }

    public function getStatusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT     => 'Borrador',
            self::STATUS_COMPLETED => 'Completado',
            self::STATUS_CANCELLED => 'Cancelado',
            default                => $this->status,
        };
    }

    public function isDraft(): bool { return $this->status === self::STATUS_DRAFT; }
}
