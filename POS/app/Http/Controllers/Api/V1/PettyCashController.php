<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\PettyCashVoucher;
use App\Services\CashMovementService;
use App\Services\ShiftService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PettyCashController extends Controller
{
    public function __construct(
        private ShiftService $shiftService,
        private CashMovementService $cashMovementService,
    ) {}

    private function denyWaiter(): ?JsonResponse
    {
        if (Auth::user()?->role === 'waiter') {
            return response()->json(['success' => false, 'message' => 'No autorizado.'], 403);
        }
        return null;
    }

    /** GET /api/v1/petty-cash/vouchers — authorized vouchers pending payment */
    public function authorizedVouchers(): JsonResponse
    {
        if ($r = $this->denyWaiter()) return $r;

        $vouchers = PettyCashVoucher::with(['requestedBy', 'category'])
            ->where('status', PettyCashVoucher::STATUS_AUTHORIZED)
            ->orderBy('authorized_at')
            ->get();

        return response()->json(['success' => true, 'data' => $vouchers]);
    }

    /** POST /api/v1/petty-cash/vouchers/{id}/pay */
    public function pay(int $id): JsonResponse
    {
        if ($r = $this->denyWaiter()) return $r;

        $voucher = PettyCashVoucher::findOrFail($id);

        if (!$voucher->isAuthorized()) {
            return response()->json([
                'success' => false,
                'message' => 'El vale no está en estado autorizado.',
            ], 422);
        }

        $shift = $this->shiftService->getActiveShift();
        if (!$shift) {
            return response()->json([
                'success' => false,
                'message' => 'No hay turno abierto. Abre un turno para pagar vales.',
            ], 422);
        }

        try {
            DB::transaction(function () use ($voucher, $shift) {
                $userId = Auth::id() ?? 0;

                $voucher->update([
                    'status'       => PettyCashVoucher::STATUS_PAID,
                    'paid_by'      => $userId,
                    'pos_shift_id' => $shift->id,
                    'paid_at'      => now(),
                ]);

                $this->cashMovementService->registerVoucherPayment($shift, $voucher, $userId);
            });

            return response()->json([
                'success' => true,
                'data'    => $voucher->fresh(['requestedBy', 'category', 'paidBy']),
                'message' => "Vale {$voucher->folio} pagado correctamente.",
            ]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }
}
