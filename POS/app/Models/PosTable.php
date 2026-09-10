<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Catálogo de mesas: se administra desde RMS (misma tabla `pos_tables`,
 * misma base de datos compartida). Este modelo es de solo lectura desde POS.
 */
class PosTable extends Model
{
    protected $fillable = [];

    protected $casts = [
        'active' => 'boolean',
    ];
}
