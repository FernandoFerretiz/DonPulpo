<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PhysicalCountItem extends Model
{
    protected $fillable = ['physical_count_id', 'inventory_product_id', 'system_quantity', 'counted_quantity', 'difference'];

    protected function casts(): array
    {
        return [
            'system_quantity'  => 'decimal:3',
            'counted_quantity' => 'decimal:3',
            'difference'       => 'decimal:3',
        ];
    }

    public function physicalCount(): BelongsTo
    {
        return $this->belongsTo(PhysicalCount::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(InventoryProduct::class, 'inventory_product_id');
    }
}
