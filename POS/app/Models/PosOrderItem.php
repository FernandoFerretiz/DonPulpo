<?php

namespace App\Models;

use App\Concerns\BelongsToCurrentBranch;
use App\Concerns\HasSyncableUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PosOrderItem extends Model
{
    use HasSyncableUuid;
    use BelongsToCurrentBranch;

    protected $table = 'pos_order_items';

    protected $fillable = [
        'pos_order_id',
        'dish_id',
        'name_snapshot',
        'unit_price',
        'quantity',
        'line_total',
        'notes',
        'branch_id',
        'sync_status',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'line_total' => 'decimal:2',
            'quantity'   => 'integer',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(PosOrder::class, 'pos_order_id');
    }

    public function dish(): BelongsTo
    {
        return $this->belongsTo(Dish::class);
    }
}
