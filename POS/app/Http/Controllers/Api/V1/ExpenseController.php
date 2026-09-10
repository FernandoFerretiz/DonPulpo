<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    private function denyWaiter(): ?JsonResponse
    {
        if (Auth::user()?->role === 'waiter') {
            return response()->json(['success' => false, 'message' => 'No autorizado.'], 403);
        }
        return null;
    }

    /**
     * GET /api/v1/expenses — gastos registrados, sin depender de un turno.
     */
    public function index(): JsonResponse
    {
        if ($r = $this->denyWaiter()) return $r;

        $expenses = Expense::with('user')
            ->orderByDesc('expense_date')
            ->orderByDesc('id')
            ->limit(100)
            ->get();

        return response()->json(['success' => true, 'data' => $expenses]);
    }

    /**
     * POST /api/v1/expenses — registrar un gasto. No requiere turno abierto:
     * puede ser una utilidad de días anteriores o una inyección personal.
     */
    public function store(Request $request): JsonResponse
    {
        if ($r = $this->denyWaiter()) return $r;

        $validated = $request->validate([
            'amount'        => 'required|numeric|min:0.01',
            'motivo'        => 'required|string|max:100',
            'description'   => 'nullable|string|max:1000',
            'expense_date'  => 'required|date|before_or_equal:today',
            // Comprobante obligatorio: sin foto del ticket no se registra el gasto.
            'ticket_photo'  => 'required|image|max:8192',
        ]);

        $photoPath = $request->file('ticket_photo')->store('expenses', 'public');

        $expense = Expense::create([
            'amount'            => $validated['amount'],
            'motivo'            => $validated['motivo'],
            'description'       => $validated['description'] ?? null,
            'expense_date'      => $validated['expense_date'],
            'ticket_photo_path' => $photoPath,
            'user_id'           => Auth::id(),
        ]);

        return response()->json([
            'success' => true,
            'data'    => $expense->load('user'),
            'message' => 'Gasto registrado.',
        ], 201);
    }
}
