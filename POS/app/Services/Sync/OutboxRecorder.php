<?php

namespace App\Services\Sync;

use App\Models\SyncEvent;
use Illuminate\Database\Eloquent\Model;

class OutboxRecorder
{
    /**
     * Record a business event in the local outbox for later push to the
     * BackOffice. $aggregate is re-fetched fresh (eager-loading $with)
     * so the payload always reflects the row as actually persisted.
     *
     * $with should list any relation whose uuid the BackOffice needs to
     * resolve a foreign key (e.g. 'user') — a POS-local numeric id means
     * nothing outside this branch's own database, so relations are how
     * the payload carries the identity that travels. Note: if a relation
     * name snake-cases to the same key as a raw FK column already in the
     * payload (e.g. a `cancelledBy()` relation vs. the `cancelled_by`
     * column), the loaded relation (object|null) wins over the raw id in
     * the final array — that's intentional, the object is what the
     * BackOffice needs.
     */
    public function record(string $eventType, Model $aggregate, array $extra = [], array $with = []): SyncEvent
    {
        $aggregate = $aggregate->fresh($with) ?? $aggregate;

        return SyncEvent::create([
            'event_type'     => $eventType,
            'aggregate_type' => class_basename($aggregate),
            'aggregate_id'   => $aggregate->getKey(),
            'aggregate_uuid' => $aggregate->uuid,
            'payload'        => array_merge($aggregate->toArray(), $extra),
        ]);
    }
}
