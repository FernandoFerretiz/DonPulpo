<?php

namespace App\Services;

use App\Models\PosOrder;
use App\Models\PosPayment;
use Illuminate\Support\Carbon;

class PaymentService
{
    public function pay(PosOrder $order, array $data, int $userId): PosPayment
    {
        if ($order->status === 'cancelled') {
            throw new \RuntimeException('No se puede cobrar una orden cancelada.');
        }

        if ($order->status === 'paid') {
            throw new \RuntimeException('La orden ya fue pagada.');
        }

        $amount = (float) ($data['amount'] ?? 0);
        $total  = (float) $order->total;

        if ($amount < $total) {
            throw new \RuntimeException("El monto recibido ({$amount}) es menor al total ({$total}).");
        }

        $method       = $data['method'] ?? 'cash';
        $changeAmount = $method === 'cash' ? round($amount - $total, 2) : 0.00;
        $paidAt       = Carbon::now();

        $payment = PosPayment::create([
            'pos_order_id'  => $order->id,
            'user_id'       => $userId,
            'method'        => $method,
            'amount'        => $amount,
            'change_amount' => $changeAmount,
            'status'        => 'paid',
            'paid_at'       => $paidAt,
        ]);

        $order->update([
            'status'  => 'paid',
            'paid_at' => $paidAt,
        ]);

        return $payment;
    }
}
