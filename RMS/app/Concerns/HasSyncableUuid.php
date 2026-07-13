<?php

namespace App\Concerns;

use Illuminate\Support\Str;

trait HasSyncableUuid
{
    protected static function bootHasSyncableUuid(): void
    {
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::ulid();
            }
        });
    }
}
