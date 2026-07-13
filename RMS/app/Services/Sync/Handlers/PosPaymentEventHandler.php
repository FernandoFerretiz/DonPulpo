<?php

namespace App\Services\Sync\Handlers;

use App\Models\Branch;
use App\Models\PosOrder;
use App\Models\PosPayment;
use App\Models\User;
use App\Services\Sync\Handlers\Concerns\ResolvesSyncedUuids;

class PosPaymentEventHandler
{
    use ResolvesSyncedUuids;

    public function handle(array $payload, Branch $branch): void
    {
        $orderId = $this->resolveId($payload['order'] ?? null, PosOrder::class);

        if (!$orderId) {
            // The order's own event hasn't been processed yet — fail so
            // this event is retried (pos_order.paid also carries the
            // same payment nested and may backfill it in the meantime,
            // making the retry a harmless idempotent no-op).
            throw new \RuntimeException('No se pudo resolver la orden para el pago; se reintentará.');
        }

        PosPayment::updateOrCreate(
            ['uuid' => $payload['uuid']],
            [
                'branch_id'     => $branch->id,
                'pos_order_id'  => $orderId,
                'user_id'       => $this->resolveId($payload['user'] ?? null, User::class),
                'method'        => $payload['method'],
                'amount'        => $payload['amount'],
                'change_amount' => $payload['change_amount'] ?? 0,
                'status'        => $payload['status'] ?? 'paid',
                'paid_at'       => $payload['paid_at'] ?? null,
            ]
        );
    }
}
