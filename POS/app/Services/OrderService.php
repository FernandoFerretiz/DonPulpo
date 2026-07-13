<?php

namespace App\Services;

use App\Models\Dish;
use App\Models\PosOrder;
use App\Models\PosOrderItem;
use App\Services\Sync\OutboxRecorder;
use Illuminate\Support\Str;

class OrderService
{
    public function __construct(private OutboxRecorder $outbox) {}

    public function createOrder(array $data, int $userId): PosOrder
    {
        $items = $data['items'] ?? [];

        $subtotal = collect($items)->sum(fn($i) => ($i['unit_price'] ?? 0) * ($i['quantity'] ?? 1));
        // Respect incoming tax (0 when IVA is disabled from the frontend)
        $tax      = isset($data['tax']) ? round((float) $data['tax'], 2) : round($subtotal * 0.16, 2);
        $tip      = round($data['tip'] ?? 0, 2);
        $total    = round($subtotal + $tax + $tip, 2);

        $order = PosOrder::create([
            'order_number'  => $this->generateOrderNumber(),
            'user_id'       => $userId,
            'customer_name' => $data['customer_name'] ?? null,
            'table_name'    => $data['table_name'] ?? null,
            'order_type'    => $data['order_type'] ?? 'dine_in',
            'subtotal'      => $subtotal,
            'tax'           => $tax,
            'tip'           => $tip,
            'total'         => $total,
            'status'        => 'open',
            'notes'         => $data['notes'] ?? null,
        ]);

        foreach ($items as $item) {
            $this->insertItem($order, $item);
        }

        $order = $order->load('items');
        $this->outbox->record('pos_order.created', $order, [], ['user', 'items.dish']);

        return $order;
    }

    public function addItemToOrder(PosOrder $order, array $item): PosOrderItem
    {
        $this->assertModifiable($order);

        $orderItem = $this->insertItem($order, $item);
        $this->recalculateTotals($order);
        $this->outbox->record('pos_order.item_added', $order, ['item_uuid' => $orderItem->uuid], ['user', 'items.dish']);

        return $orderItem;
    }

    public function updateItemQuantity(PosOrder $order, PosOrderItem $item, int $quantity): PosOrderItem
    {
        $this->assertModifiable($order);

        $item->update([
            'quantity'   => $quantity,
            'line_total' => round($item->unit_price * $quantity, 2),
        ]);

        $this->recalculateTotals($order);
        $this->outbox->record('pos_order.item_updated', $order, ['item_uuid' => $item->uuid], ['user', 'items.dish']);

        return $item->fresh();
    }

    public function removeItem(PosOrder $order, PosOrderItem $item): void
    {
        $this->assertModifiable($order);
        $itemUuid = $item->uuid;
        $item->delete();
        $this->recalculateTotals($order);
        $this->outbox->record('pos_order.item_removed', $order, ['item_uuid' => $itemUuid], ['user', 'items.dish']);
    }

    public function recalculateTotals(PosOrder $order): void
    {
        $order->refresh();
        $subtotal = $order->items()->sum('line_total');
        // Preserve the original tax rate (0% if IVA was disabled when the order was created)
        $taxRate  = ($order->subtotal > 0) ? ($order->tax / $order->subtotal) : 0.16;
        $tax      = round($subtotal * $taxRate, 2);
        $total    = round($subtotal + $tax + $order->tip, 2);

        $order->update([
            'subtotal' => $subtotal,
            'tax'      => $tax,
            'total'    => $total,
        ]);
    }

    public function cancelOrder(PosOrder $order, int $userId, ?string $reason = null): PosOrder
    {
        $this->assertModifiable($order);
        $order->update([
            'status'           => 'cancelled',
            'cancelled_by'     => $userId,
            'cancelled_at'     => now(),
            'cancelled_reason' => $reason,
        ]);
        $order = $order->fresh();
        $this->outbox->record('pos_order.cancelled', $order, [], ['user', 'items.dish', 'cancelledByUser']);

        return $order;
    }

    private function insertItem(PosOrder $order, array $item): PosOrderItem
    {
        $dish     = isset($item['dish_id']) ? Dish::find($item['dish_id']) : null;
        $name     = $item['name_snapshot'] ?? $dish?->name ?? 'Producto';
        $price    = (float) ($item['unit_price'] ?? $dish?->price ?? 0);
        $quantity = (int) ($item['quantity'] ?? 1);

        return PosOrderItem::create([
            'pos_order_id'  => $order->id,
            'dish_id'       => $dish?->id,
            'name_snapshot' => $name,
            'unit_price'    => $price,
            'quantity'      => $quantity,
            'line_total'    => round($price * $quantity, 2),
            'notes'         => $item['notes'] ?? null,
        ]);
    }

    private function assertModifiable(PosOrder $order): void
    {
        if ($order->status === 'paid') {
            throw new \RuntimeException('No se puede modificar una orden ya pagada.');
        }
        if ($order->status === 'cancelled') {
            throw new \RuntimeException('No se puede modificar una orden cancelada.');
        }
    }

    private function generateOrderNumber(): string
    {
        return 'ORD-' . strtoupper(Str::random(8));
    }
}
