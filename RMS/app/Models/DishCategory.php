<?php

namespace App\Models;

use App\Concerns\HasSyncableUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DishCategory extends Model
{
    use HasSyncableUuid;

    protected $fillable = [
        'name',
        'slug',
        'display_order',
        'status',
        'company_id',
    ];

    public const STATUSES = ['active', 'inactive'];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function dishes(): HasMany
    {
        return $this->hasMany(Dish::class);
    }

    public function activeDishes(): HasMany
    {
        return $this->hasMany(Dish::class)->where('status', 'active');
    }

    public function getStatusLabel(): string
    {
        return match($this->status) {
            'active'   => 'Activa',
            'inactive' => 'Inactiva',
            default    => $this->status,
        };
    }
}
