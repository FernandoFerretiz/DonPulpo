<?php

namespace App\Models;

use App\Concerns\HasSyncableUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasSyncableUuid;

    protected $fillable = [
        'name',
        'legal_name',
        'tax_id',
        'contact_email',
        'contact_phone',
        'plan',
        'status',
        'trial_ends_at',
    ];

    protected function casts(): array
    {
        return [
            'trial_ends_at' => 'datetime',
        ];
    }

    public const STATUSES = ['active', 'suspended', 'cancelled'];

    public function branches(): HasMany
    {
        return $this->hasMany(Branch::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
