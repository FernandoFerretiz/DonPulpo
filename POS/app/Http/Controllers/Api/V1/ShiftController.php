<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\CashMovement;
use App\Models\PosShift;
use App\Services\CashMovementService;
use App\Services\ShiftService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShiftController extends Controller
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

    /** GET /api/v1/shifts/active */
    public function active(): JsonResponse
    {
        if ($r = $this->denyWaiter()) return $r;

        $shift = $this->shiftService->getActiveShift();

        return response()->json([
            'success' => true,
            'data'    => $shift ? $shift->load('user') : null,
        ]);
    }

    /** POST /api/v1/shifts */
    public function open(Request $request): JsonResponse
    {
        if ($r = $this->denyWaiter()) return $r;

        $request->validate([
            'opening_cash' => 'required|numeric|min:0',
            'notes'        => 'nullable|string|max:500',
        ]);

        try {
            $shift = $this->shiftService->openShift(
                Auth::id() ?? 0,
                (float) $request->opening_cash,
                $request->notes,
            );
            return response()->json([
                'success' => true,
                'data'    => $shift,
                'message' => 'Turno abierto correctamente.',
            ], 201);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    /** POST /api/v1/shifts/{id}/close */
    public function close(Request $request, int $id): JsonResponse
    {
        if ($r = $this->denyWaiter()) return $r;

        $shift = PosShift::findOrFail($id);

        $request->validate([
            'counted_cash'     => 'required|numeric|min:0',
            'counted_card'     => 'nullable|numeric|min:0',
            'counted_transfer' => 'nullable|numeric|min:0',
            'notes'            => 'nullable|string|max:500',
        ]);

        try {
            $closed = $this->shiftService->closeShift(
                $shift,
                (float) $request->counted_cash,
                (float) ($request->counted_card     ?? 0),
                (float) ($request->counted_transfer ?? 0),
                $request->notes,
            );
            $summary = $this->shiftService->getShiftSummary($closed);
            return response()->json([
                'success' => true,
                'data'    => $summary,
                'message' => 'Turno cerrado correctamente.',
            ]);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    /** GET /api/v1/shifts/{id}/summary */
    public function summary(int $id): JsonResponse
    {
        if ($r = $this->denyWaiter()) return $r;

        $shift   = PosShift::findOrFail($id);
        $summary = $this->shiftService->getShiftSummary($shift);

        return response()->json(['success' => true, 'data' => $summary]);
    }

    /** POST /api/v1/shifts/{id}/movements — manual movements (retiro, ingreso, devolución) */
    public function addMovement(Request $request, int $id): JsonResponse
    {
        if ($r = $this->denyWaiter()) return $r;

        $shift = PosShift::findOrFail($id);

        $request->validate([
            'type'        => 'required|in:' . implode(',', CashMovement::MANUAL_TYPES),
            'amount'      => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:500',
        ]);

        try {
            $movement = $this->cashMovementService->registerMovement(
                $shift,
                $request->type,
                (float) $request->amount,
                ['user_id' => Auth::id() ?? 0, 'description' => $request->description],
            );
            return response()->json([
                'success' => true,
                'data'    => $movement->load('user'),
                'message' => 'Movimiento registrado.',
            ], 201);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }
}
