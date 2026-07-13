<?php

namespace App\Services\Sync\Handlers\Concerns;

trait ResolvesSyncedUuids
{
    /**
     * Payload FK fields are nested relation snapshots (or null) rather
     * than the branch-local numeric id, since a POS-local id means
     * nothing outside its own database. Resolve to this RMS's own id
     * by looking up the shared uuid.
     */
    private function resolveId(mixed $nested, string $modelClass): ?int
    {
        if (!is_array($nested) || empty($nested['uuid'])) {
            return null;
        }

        return $modelClass::query()->where('uuid', $nested['uuid'])->value('id');
    }
}
