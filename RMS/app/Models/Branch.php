<?php

namespace App\Models;

use App\Concerns\HasSyncableUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Branch extends Model
{
    use HasSyncableUuid;

    protected $fillable = [
        'company_id',
        'code',
        'name',
        'address',
        'city',
        'timezone',
        'status',
    ];

    public const STATUSES = ['active', 'inactive'];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function installations(): HasMany
    {
        return $this->hasMany(BranchInstallation::class);
    }
}
