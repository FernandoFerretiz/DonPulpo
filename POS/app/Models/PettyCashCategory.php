<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PettyCashCategory extends Model
{
    protected $fillable = ['name', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function vouchers(): HasMany
    {
        return $this->hasMany(PettyCashVoucher::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
