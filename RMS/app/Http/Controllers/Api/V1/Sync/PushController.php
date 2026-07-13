<?php

namespace App\Http\Controllers\Api\V1\Sync;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\BranchInstallation;
use App\Models\SyncEventReceipt;
use App\Services\Sync\EventDispatcher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PushController extends Controller
{
    public function __construct(private EventDispatcher $dispatcher) {}

    public function __invoke(Request $request): JsonResponse
    {
        $request->validate([
            'events'                  => 'present|array',
            'events.*.uuid'           => 'required|string',
            'events.*.event_type'     => 'required|string',
            'events.*.aggregate_type' => 'required|string',
            'events.*.aggregate_uuid' => 'required|string',
            'events.*.payload'        => 'required|array',
        ]);

        /** @var BranchInstallation $installation */
        $installation = $request->user();
        $branch       = $installation->branch;

        $installation->forceFill([
            'last_seen_at' => now(),
            'last_sync_at' => now(),
            'last_ip'      => $request->ip(),
        ])->save();

        $results = [];
        foreach ($request->input('events') as $event) {
            $results[] = $this->processOne($event, $branch, $installation);
        }

        return response()->json([
            'success'     => true,
            'server_time' => now()->toIso8601String(),
            'results'     => $results,
        ]);
    }

    private function processOne(array $event, Branch $branch, BranchInstallation $installation): array
    {
        $existing = SyncEventReceipt::where('event_uuid', $event['uuid'])->first();

        if ($existing && $existing->status === 'processed') {
            return ['uuid' => $event['uuid'], 'status' => 'confirmed'];
        }

        try {
            DB::transaction(function () use ($event, $branch, $installation) {
                $this->dispatcher->dispatch($event['aggregate_type'], $event['payload'], $branch);

                SyncEventReceipt::updateOrCreate(
                    ['event_uuid' => $event['uuid']],
                    [
                        'branch_id'              => $branch->id,
                        'branch_installation_id' => $installation->id,
                        'event_type'             => $event['event_type'],
                        'aggregate_type'         => $event['aggregate_type'],
                        'aggregate_uuid'         => $event['aggregate_uuid'],
                        'status'                 => 'processed',
                        'processed_at'           => now(),
                        'error'                  => null,
                    ]
                );
            });

            return ['uuid' => $event['uuid'], 'status' => 'confirmed'];
        } catch (\Throwable $e) {
            SyncEventReceipt::updateOrCreate(
                ['event_uuid' => $event['uuid']],
                [
                    'branch_id'              => $branch->id,
                    'branch_installation_id' => $installation->id,
                    'event_type'             => $event['event_type'],
                    'aggregate_type'         => $event['aggregate_type'],
                    'aggregate_uuid'         => $event['aggregate_uuid'],
                    'status'                 => 'failed',
                    'error'                  => $e->getMessage(),
                ]
            );

            return ['uuid' => $event['uuid'], 'status' => 'failed', 'error' => $e->getMessage()];
        }
    }
}
