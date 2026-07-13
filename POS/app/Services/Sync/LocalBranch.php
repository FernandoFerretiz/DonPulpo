<?php

namespace App\Services\Sync;

use App\Models\Branch;

/**
 * Resolves this installation's own local branch id. There is exactly
 * one row in the local `branches` table (populated by sync:pull), so
 * BelongsToCurrentBranch reads it from here instead of a static config
 * value — avoids a chicken-and-egg problem where the id isn't known
 * until after the first pull creates the row.
 */
class LocalBranch
{
    private static ?int $id = null;
    private static bool $resolved = false;

    public function id(): ?int
    {
        if (!self::$resolved) {
            self::$id = Branch::query()->value('id');
            self::$resolved = true;
        }

        return self::$id;
    }

    public function forgetCache(): void
    {
        self::$resolved = false;
        self::$id = null;
    }
}
