<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscountCode extends Model
{
    protected $fillable = [
        'code',
        'percentage',
        'beneficiary_name',
        'status',
    ];

    public const STATUSES = ['active', 'inactive'];

    public function getStatusLabel(): string
    {
        return match ($this->status) {
            'active'   => 'Activo',
            'inactive' => 'Inactivo',
            default    => $this->status,
        };
    }
}
