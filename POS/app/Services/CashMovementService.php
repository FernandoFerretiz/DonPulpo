<?php

namespace App\Services;

use App\Models\CashMovement;
use App\Models\PosOrder;
use App\Models\PosPayment;
use App\Models\PosShift;
use App\Models\PettyCashVoucher;
use App\Services\Sync\OutboxRecorder;

class CashMovementService
{
    public function __construct(private OutboxRecorder $outbox) {}

    public function registerMovement(
        PosShift $shift,
        string   $type,
        float    $amount,
        array    $extra = []
    ): CashMovement {
        if (!$shift->isOpen()) {
            throw new \RuntimeException('No se pueden registrar movimientos en un turno cerrado.');
        }

        $movement = CashMovement::create(array_merge([
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

        $this->outbox->record('cash_movement.created', $movement, [], ['shift', 'user']);

        return $movement;
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

        return $this->registerMovement($shift, $type, (float) $payment->amount, [
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
