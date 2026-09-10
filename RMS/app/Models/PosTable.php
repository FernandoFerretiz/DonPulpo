<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PosTable extends Model
{
    protected $fillable = [
        'name',
        'capacity',
        'active',
        'display_order',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];
}
