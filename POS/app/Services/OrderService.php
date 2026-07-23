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
        // Los precios ya incluyen IVA, así que no se suma impuesto adicional por defecto.
        $tax             = isset($data['tax']) ? round((float) $data['tax'], 2) : 0;
        $tip             = round($data['tip'] ?? 0, 2);
        $discountPercent = isset($data['discount_percent']) && $data['discount_percent'] !== null
            ? round((float) $data['discount_percent'], 2)
            : null;
        $discountAmount  = $discountPercent ? round($subtotal * $discountPercent / 100, 2) : 0;
        $total           = round($subtotal - $discountAmount + $tax + $tip, 2);

        $order = PosOrder::create([
            'order_number'      => $this->generateOrderNumber(),
            'user_id'           => $userId,
            'customer_name'     => $data['customer_name'] ?? null,
            'table_name'        => $data['table_name'] ?? null,
            'order_type'        => $data['order_type'] ?? 'dine_in',
            'subtotal'          => $subtotal,
            'tax'               => $tax,
            'tip'               => $tip,
            'discount_code'     => $data['discount_code'] ?? null,
            'discount_percent'  => $discountPercent,
            'discount_amount'   => $discountAmount,
            'total'             => $total,
            'status'            => 'open',
            'notes'             => $data['notes'] ?? null,
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

    public function recalculateTotals(PosOrder $order, ?float $taxOverride = null, ?float $tipOverride = null): void
    {
        $order->refresh();
        $subtotal = $order->items()->sum('line_total');
        $tip      = $tipOverride !== null ? round($tipOverride, 2) : $order->tip;
        // Los precios ya incluyen IVA: se preserva la proporción de impuesto que tuviera la orden (normalmente 0).
        $taxRate        = ($order->subtotal > 0) ? ($order->tax / $order->subtotal) : 0;
        $tax            = $taxOverride !== null ? round($taxOverride, 2) : round($subtotal * $taxRate, 2);
        $discountAmount = $order->discount_percent ? round($subtotal * $order->discount_percent / 100, 2) : 0;
        $total          = round($subtotal - $discountAmount + $tax + $tip, 2);

        $order->update([
            'subtotal'         => $subtotal,
            'tax'              => $tax,
            'tip'              => $tip,
            'discount_amount'  => $discountAmount,
            'total'            => $total,
        ]);
    }

    /**
     * Update an existing order's fields and, when items are provided, replace its item list wholesale
     * (mirrors createOrder's item handling so a re-saved order behaves like a fresh save).
     */
    public function updateOrder(PosOrder $order, array $data): PosOrder
    {
        $this->assertModifiable($order);

        $fields = [];
        foreach (['customer_name', 'table_name', 'order_type', 'notes', 'discount_code', 'discount_percent'] as $field) {
            if (array_key_exists($field, $data)) {
                $fields[$field] = $data[$field];
            }
        }
        if (array_key_exists('discount_percent', $fields) && $fields['discount_percent'] !== null) {
            $fields['discount_percent'] = round((float) $fields['discount_percent'], 2);
        }
        if ($fields) {
            $order->update($fields);
        }

        if (array_key_exists('items', $data)) {
            $order->items()->delete();
            foreach ($data['items'] as $item) {
                $this->addItemToOrder($order, $item);
            }
        }

        if (array_key_exists('tip', $data) || array_key_exists('tax', $data) || array_key_exists('items', $data)
            || array_key_exists('discount_percent', $data) || array_key_exists('discount_code', $data)) {
            $this->recalculateTotals(
                $order,
                array_key_exists('tax', $data) ? (float) $data['tax'] : null,
                array_key_exists('tip', $data) ? (float) $data['tip'] : null
            );
        }

        return $order->fresh(['items']);
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
