<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DishCategory extends Model
{
    protected $fillable = ['uuid', 'name', 'slug', 'display_order', 'status'];

    public function dishes(): HasMany
    {
        return $this->hasMany(Dish::class);
    }

    public function activeDishes(): HasMany
    {
        return $this->hasMany(Dish::class)->where('status', 'active');
    }
}
