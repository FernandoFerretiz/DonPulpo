<?php

namespace App\Services\Sync\Handlers;

use App\Models\Branch;
use App\Models\Dish;
use App\Models\PosOrder;
use App\Models\PosOrderItem;
use App\Models\PosPayment;
use App\Models\User;
use App\Services\Sync\Handlers\Concerns\ResolvesSyncedUuids;

class PosOrderEventHandler
{
    use ResolvesSyncedUuids;

    public function handle(array $payload, Branch $branch): void
    {
        $order = PosOrder::updateOrCreate(
            ['uuid' => $payload['uuid']],
            [
                'branch_id'        => $branch->id,
                'order_number'     => $payload['order_number'],
                'user_id'          => $this->resolveId($payload['user'] ?? null, User::class),
                'customer_name'    => $payload['customer_name'] ?? null,
                'table_name'       => $payload['table_name'] ?? null,
                'order_type'       => $payload['order_type'] ?? 'dine_in',
                'subtotal'         => $payload['subtotal'] ?? 0,
                'tax'              => $payload['tax'] ?? 0,
                'tip'              => $payload['tip'] ?? 0,
                'total'            => $payload['total'] ?? 0,
                'status'           => $payload['status'] ?? 'open',
                'notes'            => $payload['notes'] ?? null,
                'paid_at'          => $payload['paid_at'] ?? null,
                'cancelled_reason' => $payload['cancelled_reason'] ?? null,
                'cancelled_by'     => $this->resolveId($payload['cancelled_by_user'] ?? null, User::class),
                'cancelled_at'     => $payload['cancelled_at'] ?? null,
            ]
        );

        foreach ($payload['items'] ?? [] as $itemPayload) {
            PosOrderItem::updateOrCreate(
                ['uuid' => $itemPayload['uuid']],
                [
                    'branch_id'     => $branch->id,
                    'pos_order_id'  => $order->id,
                    'dish_id'       => $this->resolveId($itemPayload['dish'] ?? null, Dish::class),
                    'name_snapshot' => $itemPayload['name_snapshot'],
                    'unit_price'    => $itemPayload['unit_price'],
                    'quantity'      => $itemPayload['quantity'],
                    'line_total'    => $itemPayload['line_total'],
                    'notes'         => $itemPayload['notes'] ?? null,
                ]
            );
        }

        foreach ($payload['payments'] ?? [] as $paymentPayload) {
            PosPayment::updateOrCreate(
                ['uuid' => $paymentPayload['uuid']],
                [
                    'branch_id'     => $branch->id,
                    'pos_order_id'  => $order->id,
                    'user_id'       => $this->resolveId($paymentPayload['user'] ?? null, User::class),
                    'method'        => $paymentPayload['method'],
                    'amount'        => $paymentPayload['amount'],
                    'change_amount' => $paymentPayload['change_amount'] ?? 0,
                    'status'        => $paymentPayload['status'] ?? 'paid',
                    'paid_at'       => $paymentPayload['paid_at'] ?? null,
                ]
            );
        }
    }
}
