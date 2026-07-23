<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryProduct extends Model
{
    protected $fillable = [
        'name', 'internal_code', 'barcode',
        'inventory_category_id', 'unit_of_measure_id',
        'average_cost', 'last_cost', 'min_stock', 'max_stock',
        'is_active', 'tracks_inventory', 'tracks_lots', 'tracks_expiration',
    ];

    protected function casts(): array
    {
        return [
            'average_cost'      => 'decimal:4',
            'last_cost'         => 'decimal:4',
            'min_stock'         => 'decimal:3',
            'max_stock'         => 'decimal:3',
            'is_active'         => 'boolean',
            'tracks_inventory'  => 'boolean',
            'tracks_lots'       => 'boolean',
            'tracks_expiration' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(InventoryCategory::class, 'inventory_category_id');
    }

    public function unitOfMeasure(): BelongsTo
    {
        return $this->belongsTo(UnitOfMeasure::class);
    }

    public function suppliers(): BelongsToMany
    {
        return $this->belongsToMany(Supplier::class, 'inventory_product_supplier')
                     ->withPivot(['cost', 'is_primary'])
                     ->withTimestamps();
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(WarehouseStock::class);
    }

    public function movements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function stockIn(Warehouse|int $warehouse): float
    {
        $warehouseId = $warehouse instanceof Warehouse ? $warehouse->id : $warehouse;

        return (float) ($this->stocks()->where('warehouse_id', $warehouseId)->value('quantity') ?? 0);
    }

    public function totalStock(): float
    {
        return (float) $this->stocks()->sum('quantity');
    }

    public function isBelowMinimum(): bool
    {
        return $this->min_stock > 0 && $this->totalStock() < (float) $this->min_stock;
    }

    public static function generateInternalCode(): string
    {
        $prefix = 'PROD-';
        $last   = static::where('internal_code', 'like', $prefix . '%')->orderByDesc('id')->first();
        $seq    = $last ? ((int) substr($last->internal_code, strlen($prefix))) + 1 : 1;
        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Costo promedio ponderado: se recalcula ANTES de aplicar la entrada de
     * stock de la compra (usa la existencia total previa a esta compra).
     */
    public function applyPurchaseCost(float $quantity, float $unitCost): void
    {
        $previousStock = $this->totalStock();
        $previousValue = $previousStock * (float) $this->average_cost;
        $newValue      = $previousValue + ($quantity * $unitCost);
        $newTotal      = $previousStock + $quantity;

        $this->update([
            'last_cost'    => $unitCost,
            'average_cost' => $newTotal > 0 ? $newValue / $newTotal : $unitCost,
        ]);
    }
}
