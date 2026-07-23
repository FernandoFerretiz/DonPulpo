<?php

namespace App\Services;

use App\Models\CashMovement;
use App\Models\PosOrder;
use App\Models\PosPayment;
use App\Models\PosShift;
use App\Models\PettyCashVoucher;

class CashMovementService
{
    public function registerMovement(
        PosShift $shift,
        string   $type,
        float    $amount,
        array    $extra = []
    ): CashMovement {
        if (!$shift->isOpen()) {
            throw new \RuntimeException('No se pueden registrar movimientos en un turno cerrado.');
        }

        return CashMovement::create(array_merge([
            'pos_shift_id' => $shift->id,
            'user_id'      => $extra['user_id'] ?? $shift->user_id,
            'type'         => $type,
            'amount'       => round(abs($amount), 2),
        ], array_filter([
            'payment_method' => $extra['payment_method'] ?? null,
            'description'    => $extra['description']    ?? null,
            'reference_type' => $extra['reference_type'] ?? null,
            'reference_id'   => $extra['reference_id']   ?? null,
        ], fn($v) => $v !== null)));
    }

    public function registerSalePayment(
        PosShift   $shift,
        PosOrder   $order,
        PosPayment $payment,
        int        $userId
    ): CashMovement {
        $type = match ($payment->method) {
            'cash'     => CashMovement::TYPE_VENTA_EFECTIVO,
            'card'     => CashMovement::TYPE_VENTA_TARJETA,
            'transfer' => CashMovement::TYPE_VENTA_TRANSFERENCIA,
            default    => CashMovement::TYPE_VENTA_EFECTIVO,
        };

        // El efectivo tendido menos el cambio entregado es lo que realmente
        // queda en caja; en tarjeta/transferencia no aplica cambio.
        $netAmount = $payment->method === 'cash'
            ? (float) $payment->amount - (float) $payment->change_amount
            : (float) $payment->amount;

        return $this->registerMovement($shift, $type, $netAmount, [
            'user_id'        => $userId,
            'payment_method' => $payment->method,
            'description'    => "Orden {$order->order_number}",
            'reference_type' => PosPayment::class,
            'reference_id'   => $payment->id,
        ]);
    }

    public function registerVoucherPayment(
        PosShift         $shift,
        PettyCashVoucher $voucher,
        int              $userId
    ): CashMovement {
        return $this->registerMovement($shift, CashMovement::TYPE_VALE_CAJA_CHICA, (float) $voucher->amount, [
            'user_id'        => $userId,
            'description'    => "Vale {$voucher->folio}: {$voucher->concept}",
            'reference_type' => PettyCashVoucher::class,
            'reference_id'   => $voucher->id,
        ]);
    }
}
