<?php

namespace App\Services\Sync\Handlers;

use App\Models\Branch;
use App\Models\PosShift;
use App\Models\User;
use App\Services\Sync\Handlers\Concerns\ResolvesSyncedUuids;

class PosShiftEventHandler
{
    use ResolvesSyncedUuids;

    public function handle(array $payload, Branch $branch): void
    {
        PosShift::updateOrCreate(
            ['uuid' => $payload['uuid']],
            [
                'branch_id'        => $branch->id,
                'user_id'          => $this->resolveId($payload['user'] ?? null, User::class),
                'terminal_id'      => $payload['terminal_id'] ?? null,
                'status'           => $payload['status'] ?? 'open',
                'opening_cash'     => $payload['opening_cash'] ?? 0,
                'expected_cash'    => $payload['expected_cash'] ?? null,
                'counted_cash'     => $payload['counted_cash'] ?? null,
                'counted_card'     => $payload['counted_card'] ?? null,
                'counted_transfer' => $payload['counted_transfer'] ?? null,
                'difference'       => $payload['difference'] ?? null,
                'opened_at'        => $payload['opened_at'] ?? null,
                'closed_at'        => $payload['closed_at'] ?? null,
                'notes'            => $payload['notes'] ?? null,
            ]
        );
    }
}
