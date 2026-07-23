<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UnitOfMeasure extends Model
{
    protected $table = 'units_of_measure';

    protected $fillable = ['name', 'abbreviation', 'base_unit_id', 'conversion_factor'];

    protected function casts(): array
    {
        return ['conversion_factor' => 'decimal:4'];
    }

    public function baseUnit(): BelongsTo
    {
        return $this->belongsTo(UnitOfMeasure::class, 'base_unit_id');
    }

    /**
     * Cuántas unidades base equivalen a $quantity de esta unidad.
     * Ej.: 2 kilogramos (factor 1000) -> 2000 gramos.
     */
    public function toBaseQuantity(float $quantity): float
    {
        return $quantity * (float) $this->conversion_factor;
    }
}
