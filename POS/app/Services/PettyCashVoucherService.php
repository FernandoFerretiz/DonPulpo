<?php

namespace App\Services;

use App\Models\PettyCashVoucher;
use App\Models\PosShift;
use App\Services\Sync\OutboxRecorder;
use Illuminate\Support\Facades\DB;

class PettyCashVoucherService
{
    public function __construct(
        private CashMovementService $cashMovementService,
        private OutboxRecorder $outbox,
    ) {}

    public function request(array $data, int $userId): PettyCashVoucher
    {
        $voucher = PettyCashVoucher::create([
            'folio'                   => PettyCashVoucher::generateFolio(),
            'requested_by'            => $userId,
            'petty_cash_category_id'  => $data['petty_cash_category_id'] ?? null,
            'beneficiary'             => $data['beneficiary'] ?? null,
            'concept'                 => $data['concept'],
            'amount'                  => $data['amount'],
            'notes'                   => $data['notes'] ?? null,
            'status'                  => PettyCashVoucher::STATUS_PENDING,
            'requested_at'            => now(),
        ]);

        $this->outbox->record('petty_cash_voucher.requested', $voucher, [], ['requestedBy', 'category']);

        return $voucher;
    }

    public function authorize(PettyCashVoucher $voucher, int $userId): PettyCashVoucher
    {
        if (!$voucher->isPending()) {
            throw new \RuntimeException('Solo se pueden autorizar vales pendientes.');
        }

        $voucher->update([
            'status'        => PettyCashVoucher::STATUS_AUTHORIZED,
            'authorized_by' => $userId,
            'authorized_at' => now(),
        ]);

        $voucher = $voucher->fresh();
        $this->outbox->record('petty_cash_voucher.authorized', $voucher, [], ['requestedBy', 'authorizedBy', 'category']);

        return $voucher;
    }

    public function reject(PettyCashVoucher $voucher, int $userId, string $reason): PettyCashVoucher
    {
        if (!$voucher->isPending()) {
            throw new \RuntimeException('Solo se pueden rechazar vales pendientes.');
        }

        $voucher->update([
            'status'           => PettyCashVoucher::STATUS_REJECTED,
            'rejected_by'      => $userId,
            'rejected_at'      => now(),
            'rejection_reason' => $reason,
        ]);

        $voucher = $voucher->fresh();
        $this->outbox->record('petty_cash_voucher.rejected', $voucher, [], ['requestedBy', 'rejectedBy', 'category']);

        return $voucher;
    }

    public function cancel(PettyCashVoucher $voucher): PettyCashVoucher
    {
        if ($voucher->isPaid()) {
            throw new \RuntimeException('No se puede cancelar un vale ya pagado.');
        }

        $voucher->update([
            'status'       => PettyCashVoucher::STATUS_CANCELLED,
            'cancelled_at' => now(),
        ]);

        $voucher = $voucher->fresh();
        $this->outbox->record('petty_cash_voucher.cancelled', $voucher, [], ['requestedBy', 'category']);

        return $voucher;
    }

    public function pay(PettyCashVoucher $voucher, PosShift $shift, int $userId): PettyCashVoucher
    {
        if (!$voucher->isAuthorized()) {
            throw new \RuntimeException('El vale no está en estado autorizado.');
        }

        $voucher = DB::transaction(function () use ($voucher, $shift, $userId) {
            $voucher->update([
                'status'       => PettyCashVoucher::STATUS_PAID,
                'paid_by'      => $userId,
                'pos_shift_id' => $shift->id,
                'paid_at'      => now(),
            ]);

            $this->cashMovementService->registerVoucherPayment($shift, $voucher, $userId);

            return $voucher->fresh();
        });

        $this->outbox->record('petty_cash_voucher.paid', $voucher, [], ['requestedBy', 'paidBy', 'category', 'shift']);

        return $voucher;
    }
}
