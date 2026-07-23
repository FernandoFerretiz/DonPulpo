<?php

namespace App\Services;

use App\Models\CashMovement;
use App\Models\PosShift;
use Illuminate\Support\Facades\DB;

class ShiftService
{
    public function getActiveShift(): ?PosShift
    {
        return PosShift::open()->latest('opened_at')->first();
    }

    public function openShift(int $userId, float $openingCash, ?string $notes = null): PosShift
    {
        if ($this->getActiveShift()) {
            throw new \RuntimeException('Ya existe un turno abierto. Ciérralo antes de abrir uno nuevo.');
        }

        return DB::transaction(function () use ($userId, $openingCash, $notes) {
            $shift = PosShift::create([
                'user_id'      => $userId,
                'status'       => PosShift::STATUS_OPEN,
                'opening_cash' => round($openingCash, 2),
                'opened_at'    => now(),
                'notes'        => $notes,
            ]);

            CashMovement::create([
                'pos_shift_id' => $shift->id,
                'user_id'      => $userId,
                'type'         => CashMovement::TYPE_FONDO_INICIAL,
                'amount'       => round($openingCash, 2),
                'description'  => 'Fondo inicial de turno',
            ]);

            return $shift->load('user');
        });
    }

    public function closeShift(
        PosShift $shift,
        float $countedCash,
        float $countedCard = 0,
        float $countedTransfer = 0,
        ?string $notes = null
    ): PosShift {
        if (!$shift->isOpen()) {
            throw new \RuntimeException('El turno ya está cerrado o cancelado.');
        }

        return DB::transaction(function () use ($shift, $countedCash, $countedCard, $countedTransfer, $notes) {
            $expectedCash = $this->calculateExpectedCash($shift);
            $difference   = round($countedCash - $expectedCash, 2);

            $shift->update([
                'status'           => PosShift::STATUS_CLOSED,
                'expected_cash'    => $expectedCash,
                'counted_cash'     => round($countedCash, 2),
                'counted_card'     => round($countedCard, 2),
                'counted_transfer' => round($countedTransfer, 2),
                'difference'       => $difference,
                'closed_at'        => now(),
                'notes'            => $notes ?? $shift->notes,
            ]);

            return $shift->fresh();
        });
    }

    public function calculateExpectedCash(PosShift $shift): float
    {
        $income = (float) $shift->cashMovements()
            ->whereIn('type', CashMovement::INCOME_TYPES)
            ->sum('amount');

        $expenses = (float) $shift->cashMovements()
            ->whereIn('type', CashMovement::EXPENSE_TYPES)
            ->sum('amount');

        return round($income - $expenses, 2);
    }

    public function getShiftSummary(PosShift $shift): array
    {
        $movements = $shift->cashMovements()
            ->with('user')
            ->orderBy('created_at')
            ->get();

        return [
            'shift'     => $shift->load('user'),
            'movements' => $movements,
            'totals'    => [
                'opening_cash'   => (float) $movements->where('type', CashMovement::TYPE_FONDO_INICIAL)->sum('amount'),
                'sales_cash'     => (float) $movements->where('type', CashMovement::TYPE_VENTA_EFECTIVO)->sum('amount'),
                'sales_card'     => (float) $movements->where('type', CashMovement::TYPE_VENTA_TARJETA)->sum('amount'),
                'sales_transfer' => (float) $movements->where('type', CashMovement::TYPE_VENTA_TRANSFERENCIA)->sum('amount'),
                'income_manual'  => (float) $movements->where('type', CashMovement::TYPE_INGRESO_MANUAL)->sum('amount'),
                'vouchers_paid'  => (float) $movements->where('type', CashMovement::TYPE_VALE_CAJA_CHICA)->sum('amount'),
                'withdrawals'    => (float) $movements->where('type', CashMovement::TYPE_RETIRO_EFECTIVO)->sum('amount'),
                'returns'        => (float) $movements->where('type', CashMovement::TYPE_DEVOLUCION_EFECTIVO)->sum('amount'),
                'expected_cash'  => $this->calculateExpectedCash($shift),
                'counted_cash'     => $shift->counted_cash     !== null ? (float) $shift->counted_cash     : null,
                'counted_card'     => $shift->counted_card     !== null ? (float) $shift->counted_card     : null,
                'counted_transfer' => $shift->counted_transfer !== null ? (float) $shift->counted_transfer : null,
                'difference'       => $shift->difference       !== null ? (float) $shift->difference       : null,
            ],
        ];
    }
}
