<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PosOrderItemModifier extends Model
{
    protected $table = 'pos_order_item_modifiers';

    protected $fillable = [
        'pos_order_item_id',
        'modifier_option_id',
        'group_name_snapshot',
        'option_name_snapshot',
        'price_delta_snapshot',
    ];

    protected function casts(): array
    {
        return [
            'price_delta_snapshot' => 'decimal:2',
        ];
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(PosOrderItem::class, 'pos_order_item_id');
    }
}
