<?php

namespace App\Models;

use App\Concerns\BelongsToCurrentBranch;
use App\Concerns\HasSyncableUuid;
use Illuminate\Database\Eloquent\Model;

class SyncEvent extends Model
{
    use HasSyncableUuid;
    use BelongsToCurrentBranch;

    protected $fillable = [
        'event_type',
        'aggregate_type',
        'aggregate_id',
        'aggregate_uuid',
        'payload',
        'sync_status',
        'attempts',
        'last_attempted_at',
        'last_error',
        'confirmed_at',
    ];

    protected function casts(): array
    {
        return [
            'payload'            => 'array',
            'last_attempted_at'  => 'datetime',
            'confirmed_at'       => 'datetime',
        ];
    }

    public function scopePending($query)
    {
        return $query->whereIn('sync_status', ['pending', 'failed']);
    }
}
