<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SyncEventReceipt extends Model
{
    protected $fillable = [
        'event_uuid',
        'branch_id',
        'branch_installation_id',
        'event_type',
        'aggregate_type',
        'aggregate_uuid',
        'status',
        'error',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'processed_at' => 'datetime',
        ];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function installation(): BelongsTo
    {
        return $this->belongsTo(BranchInstallation::class, 'branch_installation_id');
    }
}
