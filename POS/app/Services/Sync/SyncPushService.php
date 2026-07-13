<?php

namespace App\Services\Sync;

use App\Models\SyncEvent;

class SyncPushService
{
    public function __construct(private InstallationClient $client) {}

    public function push(int $batchSize = 50): array
    {
        $events = SyncEvent::pending()->orderBy('id')->limit($batchSize)->get();

        if ($events->isEmpty()) {
            return ['sent' => 0, 'confirmed' => 0, 'failed' => 0];
        }

        foreach ($events as $event) {
            $event->increment('attempts');
            $event->update(['sync_status' => 'sending', 'last_attempted_at' => now()]);
        }

        $payload = [
            'events' => $events->map(fn (SyncEvent $e) => [
                'uuid'           => $e->uuid,
                'event_type'     => $e->event_type,
                'aggregate_type' => $e->aggregate_type,
                'aggregate_uuid' => $e->aggregate_uuid,
                'occurred_at'    => $e->created_at->toIso8601String(),
                'payload'        => $e->payload,
            ])->values()->all(),
        ];

        try {
            $response = $this->client->client()->post('push', $payload)->throw()->json();
        } catch (\Throwable $e) {
            SyncEvent::whereIn('id', $events->pluck('id'))->update([
                'sync_status' => 'failed',
                'last_error'  => $e->getMessage(),
            ]);

            return ['sent' => $events->count(), 'confirmed' => 0, 'failed' => $events->count(), 'error' => $e->getMessage()];
        }

        $results    = collect($response['results'] ?? [])->keyBy('uuid');
        $maxAttempts = (int) config('sync.max_attempts', 20);
        $confirmed  = 0;
        $failed     = 0;

        foreach ($events as $event) {
            $result = $results->get($event->uuid);

            if (($result['status'] ?? null) === 'confirmed') {
                $event->update(['sync_status' => 'confirmed', 'confirmed_at' => now(), 'last_error' => null]);
                $confirmed++;
                continue;
            }

            $event->update([
                'sync_status' => $event->attempts >= $maxAttempts ? 'stuck' : 'failed',
                'last_error'  => $result['error'] ?? 'Sin confirmación del servidor.',
            ]);
            $failed++;
        }

        return ['sent' => $events->count(), 'confirmed' => $confirmed, 'failed' => $failed];
    }
}
