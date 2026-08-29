<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'balance',
        'credit_limit',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'balance'      => 'decimal:2',
            'credit_limit' => 'decimal:2',
        ];
    }

    public function availableCredit(): ?float
    {
        if ($this->credit_limit === null) {
            return null;
        }

        return (float) $this->credit_limit - (float) $this->balance;
    }
}
