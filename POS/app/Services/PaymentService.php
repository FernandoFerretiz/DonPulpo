<?php

namespace App\Services;

use App\Models\PosOrder;
use App\Models\PosPayment;
use Illuminate\Support\Carbon;

class PaymentService
{
    /**
     * Register one or more payments for an order.
     * The sum of all payment amounts must be >= order total.
     */
    public function payMultiple(PosOrder $order, array $payments, int $userId): array
    {
        if ($order->status === 'cancelled') {
            throw new \RuntimeException('No se puede cobrar una orden cancelada.');
        }
        if ($order->status === 'paid') {
            throw new \RuntimeException('La orden ya fue pagada.');
        }

        $orderTotal = (float) $order->total;
        $totalPaid  = collect($payments)->sum(fn($p) => (float) ($p['amount'] ?? 0));

        if ($totalPaid < $orderTotal) {
            $diff = number_format($orderTotal - $totalPaid, 2);
            throw new \RuntimeException("Faltan \${$diff} para completar el pago.");
        }

        $change  = round($totalPaid - $orderTotal, 2);
        $paidAt  = Carbon::now();
        $records = [];
        $changeAssigned = false;

        foreach ($payments as $p) {
            $method = $p['method'] ?? 'cash';
            $amount = round((float) ($p['amount'] ?? 0), 2);

            // Assign overpayment change to the first cash payment
            $changeAmount = 0;
            if (!$changeAssigned && $method === 'cash' && $change > 0) {
                $changeAmount   = $change;
                $changeAssigned = true;
            }

            $records[] = PosPayment::create([
                'pos_order_id'  => $order->id,
                'user_id'       => $userId,
                'method'        => $method,
                'amount'        => $amount,
                'change_amount' => $changeAmount,
                'status'        => 'paid',
                'paid_at'       => $paidAt,
            ]);
        }

        $order->update([
            'status'  => 'paid',
            'paid_at' => $paidAt,
        ]);

        return [
            'payments'      => $records,
            'total_paid'    => round($totalPaid, 2),
            'change_amount' => $change,
            'paid_at'       => $paidAt,
        ];
    }
}
