<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\PettyCashVoucher;
use App\Services\PettyCashVoucherService;
use App\Services\ShiftService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PettyCashController extends Controller
{
    public function __construct(
        private ShiftService $shiftService,
        private PettyCashVoucherService $pettyCashVoucherService,
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

    /** GET /api/v1/petty-cash/vouchers/history — full list, optionally filtered by status */
    public function index(Request $request): JsonResponse
    {
        if ($r = $this->denyWaiter()) return $r;

        $query = PettyCashVoucher::with(['requestedBy', 'authorizedBy', 'rejectedBy', 'paidBy', 'category'])
            ->orderByDesc('created_at');

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        return response()->json(['success' => true, 'data' => $query->paginate(20)]);
    }

    /** POST /api/v1/petty-cash/vouchers — request a new voucher */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'petty_cash_category_id' => 'nullable|integer|exists:petty_cash_categories,id',
            'beneficiary'            => 'nullable|string|max:255',
            'concept'                => 'required|string|max:1000',
            'amount'                 => 'required|numeric|min:0.01',
            'notes'                  => 'nullable|string|max:1000',
        ]);

        $voucher = $this->pettyCashVoucherService->request($request->all(), Auth::id() ?? 0);

        return response()->json([
            'success' => true,
            'data'    => $voucher,
            'message' => "Vale {$voucher->folio} solicitado correctamente.",
        ], 201);
    }

    /** PATCH /api/v1/petty-cash/vouchers/{id}/authorize */
    public function authorize(int $id): JsonResponse
    {
        if ($r = $this->denyWaiter()) return $r;

        $voucher = PettyCashVoucher::findOrFail($id);

        try {
            $voucher = $this->pettyCashVoucherService->authorize($voucher, Auth::id() ?? 0);
            return response()->json(['success' => true, 'data' => $voucher, 'message' => "Vale {$voucher->folio} autorizado."]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    /** PATCH /api/v1/petty-cash/vouchers/{id}/reject */
    public function reject(Request $request, int $id): JsonResponse
    {
        if ($r = $this->denyWaiter()) return $r;

        $request->validate(['rejection_reason' => 'required|string|max:500']);

        $voucher = PettyCashVoucher::findOrFail($id);

        try {
            $voucher = $this->pettyCashVoucherService->reject($voucher, Auth::id() ?? 0, $request->rejection_reason);
            return response()->json(['success' => true, 'data' => $voucher, 'message' => "Vale {$voucher->folio} rechazado."]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    /** PATCH /api/v1/petty-cash/vouchers/{id}/cancel */
    public function cancel(int $id): JsonResponse
    {
        if ($r = $this->denyWaiter()) return $r;

        $voucher = PettyCashVoucher::findOrFail($id);

        try {
            $voucher = $this->pettyCashVoucherService->cancel($voucher);
            return response()->json(['success' => true, 'data' => $voucher, 'message' => "Vale {$voucher->folio} cancelado."]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    /** POST /api/v1/petty-cash/vouchers/{id}/pay */
    public function pay(int $id): JsonResponse
    {
        if ($r = $this->denyWaiter()) return $r;

        $voucher = PettyCashVoucher::findOrFail($id);

        $shift = $this->shiftService->getActiveShift();
        if (!$shift) {
            return response()->json([
                'success' => false,
                'message' => 'No hay turno abierto. Abre un turno para pagar vales.',
            ], 422);
        }

        try {
            $voucher = $this->pettyCashVoucherService->pay($voucher, $shift, Auth::id() ?? 0);
            return response()->json([
                'success' => true,
                'data'    => $voucher->load(['requestedBy', 'category', 'paidBy']),
                'message' => "Vale {$voucher->folio} pagado correctamente.",
            ]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }
}
