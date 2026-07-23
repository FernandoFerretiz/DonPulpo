<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShrinkageItem extends Model
{
    protected $fillable = ['shrinkage_id', 'inventory_product_id', 'quantity', 'unit_cost'];

    protected function casts(): array
    {
        return [
            'quantity'  => 'decimal:3',
            'unit_cost' => 'decimal:4',
        ];
    }

    public function shrinkage(): BelongsTo
    {
        return $this->belongsTo(Shrinkage::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(InventoryProduct::class, 'inventory_product_id');
    }
}
