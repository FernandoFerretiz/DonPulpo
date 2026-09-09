<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PosOrderNumberSetting extends Model
{
    protected $table = 'pos_order_number_settings';

    protected $fillable = [
        'mode',
        'next_number',
    ];

    // Número de sucursal fijo hasta que exista soporte multi-sucursal.
    public const BRANCH_NUMBER = 1;

    public static function current(): self
    {
        return static::query()->firstOrCreate([], [
            'mode'        => 'sequential',
            'next_number' => 100001,
        ]);
    }
}
