<?php

namespace App\Concerns;

use App\Services\Sync\LocalBranch;

trait BelongsToCurrentBranch
{
    protected static function bootBelongsToCurrentBranch(): void
    {
        static::creating(function ($model) {
            if (empty($model->branch_id)) {
                $model->branch_id = app(LocalBranch::class)->id();
            }
        });
    }
}
