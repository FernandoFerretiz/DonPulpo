<?php

namespace App\Models;

use App\Concerns\HasSyncableUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Sanctum\HasApiTokens;

/**
 * The "device" identity for a branch's local server. Sanctum tokens are
 * issued against this model, not against a human User, so sync never
 * needs a person's credentials.
 */
class BranchInstallation extends Model
{
    use HasApiTokens;
    use HasSyncableUuid;

    protected $fillable = [
        'branch_id',
        'device_name',
        'app_version',
        'status',
        'last_seen_at',
        'last_sync_at',
        'last_ip',
    ];

    protected function casts(): array
    {
        return [
            'last_seen_at' => 'datetime',
            'last_sync_at' => 'datetime',
        ];
    }

    public const STATUSES = ['pending', 'active', 'revoked'];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
