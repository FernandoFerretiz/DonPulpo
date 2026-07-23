<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Warehouse extends Model
{
    protected $fillable = ['name', 'slug', 'status'];

    const STATUSES = ['active', 'inactive'];

    public function stocks(): HasMany
    {
        return $this->hasMany(WarehouseStock::class);
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
