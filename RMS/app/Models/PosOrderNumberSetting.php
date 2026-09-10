<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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

    /**
     * Genera el siguiente order_number según el modo configurado (sin prefijo de texto:
     * el número de sucursal va concatenado al inicio del número).
     */
    public static function generateOrderNumber(): string
    {
        return DB::transaction(function () {
            $setting = static::query()->lockForUpdate()->first();
            if (! $setting) {
                static::current();
                $setting = static::query()->lockForUpdate()->first();
            }

            if ($setting->mode === 'random') {
                do {
                    $candidate = self::BRANCH_NUMBER . random_int(100000, 999999);
                } while (PosOrder::where('order_number', $candidate)->exists());

                return $candidate;
            }

            $orderNumber = self::BRANCH_NUMBER . $setting->next_number;
            $setting->increment('next_number');

            return $orderNumber;
        });
    }
}
