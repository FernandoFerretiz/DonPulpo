<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryCategory extends Model
{
    protected $fillable = ['name', 'slug', 'status'];

    const STATUSES = ['active', 'inactive'];

    public function products(): HasMany
    {
        return $this->hasMany(InventoryProduct::class);
    }

    public function getStatusLabel(): string
    {
        return match ($this->status) {
            'active'   => 'Activa',
            'inactive' => 'Inactiva',
            default    => $this->status,
        };
    }
}
