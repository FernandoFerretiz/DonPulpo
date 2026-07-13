<?php

namespace App\Services\Sync\Handlers;

use App\Models\Branch;
use App\Models\CashMovement;
use App\Models\PosShift;
use App\Models\User;
use App\Services\Sync\Handlers\Concerns\ResolvesSyncedUuids;

class CashMovementEventHandler
{
    use ResolvesSyncedUuids;

    public function handle(array $payload, Branch $branch): void
    {
        $shiftId = $this->resolveId($payload['shift'] ?? null, PosShift::class);

        if (!$shiftId) {
            // The shift's own event hasn't been processed yet — fail so
            // this event is retried on the next push instead of being
            // silently dropped.
            throw new \RuntimeException('No se pudo resolver el turno para el movimiento de caja; se reintentará.');
        }

        CashMovement::updateOrCreate(
            ['uuid' => $payload['uuid']],
            [
                'branch_id'      => $branch->id,
                'pos_shift_id'   => $shiftId,
                'user_id'        => $this->resolveId($payload['user'] ?? null, User::class),
                'type'           => $payload['type'],
                'amount'         => $payload['amount'],
                'payment_method' => $payload['payment_method'] ?? null,
                'description'    => $payload['description'] ?? null,
            ]
        );
    }
}
