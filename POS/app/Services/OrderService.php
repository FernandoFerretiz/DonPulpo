<?php

namespace App\Services;

use App\Models\Dish;
use App\Models\PosOrder;
use App\Models\PosOrderItem;
use Illuminate\Support\Str;

class OrderService
{
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
            'subtotal'      => $subtotal,
            'tax'           => $tax,
            'tip'           => $tip,
            'total'         => $total,
            'status'        => 'open',
            'notes'         => $data['notes'] ?? null,
        ]);

        foreach ($items as $item) {
            $this->addItemToOrder($order, $item);
        }

        return $order->load('items');
    }

    public function addItemToOrder(PosOrder $order, array $item): PosOrderItem
    {
        $this->assertModifiable($order);

        $dish     = isset($item['dish_id']) ? Dish::find($item['dish_id']) : null;
        $name     = $item['name_snapshot'] ?? $dish?->name ?? 'Producto';
        $price    = (float) ($item['unit_price'] ?? $dish?->price ?? 0);
        $quantity = (int) ($item['quantity'] ?? 1);

        $orderItem = PosOrderItem::create([
            'pos_order_id'  => $order->id,
            'dish_id'       => $dish?->id,
            'name_snapshot' => $name,
            'unit_price'    => $price,
            'quantity'      => $quantity,
            'line_total'    => round($price * $quantity, 2),
            'notes'         => $item['notes'] ?? null,
        ]);

        $this->recalculateTotals($order);

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

        return $item->fresh();
    }

    public function removeItem(PosOrder $order, PosOrderItem $item): void
    {
        $this->assertModifiable($order);
        $item->delete();
        $this->recalculateTotals($order);
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

    public function cancelOrder(PosOrder $order): PosOrder
    {
        $this->assertModifiable($order);
        $order->update(['status' => 'cancelled']);
        return $order->fresh();
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
