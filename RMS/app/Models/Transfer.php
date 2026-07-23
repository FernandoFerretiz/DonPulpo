<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transfer extends Model
{
    protected $fillable = [
        'folio', 'origin_warehouse_id', 'destination_warehouse_id',
        'transfer_date', 'notes', 'status', 'created_by',
    ];

    protected function casts(): array
    {
        return ['transfer_date' => 'date'];
    }

    const STATUS_DRAFT     = 'draft';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    const STATUSES = [self::STATUS_DRAFT, self::STATUS_COMPLETED, self::STATUS_CANCELLED];

    public function originWarehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'origin_warehouse_id');
    }

    public function destinationWarehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'destination_warehouse_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(TransferItem::class);
    }

    public static function generateFolio(): string
    {
        $prefix = 'TRA-' . now()->format('Ymd') . '-';
        $last   = static::where('folio', 'like', $prefix . '%')->orderByDesc('id')->first();
        $seq    = $last ? ((int) substr($last->folio, -3)) + 1 : 1;
        return $prefix . str_pad($seq, 3, '0', STR_PAD_LEFT);
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
