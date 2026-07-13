<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Local cache of this installation's own branch. Populated by
 * install:register / sync:pull — never written to from the POS UI.
 */
class Branch extends Model
{
    protected $fillable = [
        'uuid',
        'company_uuid',
        'code',
        'name',
        'address',
        'city',
        'timezone',
        'status',
    ];
}
