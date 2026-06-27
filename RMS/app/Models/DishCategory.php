<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DishCategory extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'display_order',
        'status',
    ];

    public const STATUSES = ['active', 'inactive'];

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
