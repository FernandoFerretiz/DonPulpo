<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdjustmentItem extends Model
{
    protected $fillable = ['adjustment_id', 'inventory_product_id', 'previous_quantity', 'new_quantity', 'difference'];

    protected function casts(): array
    {
        return [
            'previous_quantity' => 'decimal:3',
            'new_quantity'      => 'decimal:3',
            'difference'        => 'decimal:3',
        ];
    }

    public function adjustment(): BelongsTo
    {
        return $this->belongsTo(Adjustment::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(InventoryProduct::class, 'inventory_product_id');
    }
}
