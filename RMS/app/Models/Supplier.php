<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Supplier extends Model
{
    protected $fillable = ['name', 'contact_name', 'phone', 'email', 'address', 'status'];

    const STATUSES = ['active', 'inactive'];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(InventoryProduct::class, 'inventory_product_supplier')
                     ->withPivot(['cost', 'is_primary'])
                     ->withTimestamps();
    }

    public function getStatusLabel(): string
    {
        return match ($this->status) {
            'active'   => 'Activo',
            'inactive' => 'Inactivo',
            default    => $this->status,
        };
    }
}
