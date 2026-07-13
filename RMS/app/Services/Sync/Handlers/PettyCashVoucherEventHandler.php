<?php

namespace App\Services\Sync\Handlers;

use App\Models\Branch;
use App\Models\PettyCashCategory;
use App\Models\PettyCashVoucher;
use App\Models\PosShift;
use App\Models\User;
use App\Services\Sync\Handlers\Concerns\ResolvesSyncedUuids;

class PettyCashVoucherEventHandler
{
    use ResolvesSyncedUuids;

    public function handle(array $payload, Branch $branch): void
    {
        PettyCashVoucher::updateOrCreate(
            ['uuid' => $payload['uuid']],
            [
                'branch_id'              => $branch->id,
                'folio'                  => $payload['folio'],
                'requested_by'           => $this->resolveId($payload['requested_by'] ?? null, User::class),
                'authorized_by'          => $this->resolveId($payload['authorized_by'] ?? null, User::class),
                'rejected_by'            => $this->resolveId($payload['rejected_by'] ?? null, User::class),
                'paid_by'                => $this->resolveId($payload['paid_by'] ?? null, User::class),
                'pos_shift_id'           => $this->resolveId($payload['shift'] ?? null, PosShift::class),
                'petty_cash_category_id' => $this->resolveId($payload['category'] ?? null, PettyCashCategory::class),
                'beneficiary'            => $payload['beneficiary'] ?? null,
                'concept'                => $payload['concept'],
                'amount'                 => $payload['amount'],
                'status'                 => $payload['status'] ?? 'pending',
                'requested_at'           => $payload['requested_at'] ?? null,
                'authorized_at'          => $payload['authorized_at'] ?? null,
                'rejected_at'            => $payload['rejected_at'] ?? null,
                'paid_at'                => $payload['paid_at'] ?? null,
                'cancelled_at'           => $payload['cancelled_at'] ?? null,
                'rejection_reason'       => $payload['rejection_reason'] ?? null,
                'notes'                  => $payload['notes'] ?? null,
            ]
        );
    }
}
