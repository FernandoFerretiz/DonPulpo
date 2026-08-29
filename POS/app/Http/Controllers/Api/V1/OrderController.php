<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\PosOrder;
use App\Models\PosOrderItem;
use App\Services\CashMovementService;
use App\Services\OrderService;
use App\Services\PaymentService;
use App\Services\ShiftService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderController extends Controller
{
    public function __construct(
        private OrderService $orderService,
        private PaymentService $paymentService,
        private ShiftService $shiftService,
        private CashMovementService $cashMovementService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $query = PosOrder::with(['items', 'user', 'cancelledBy'])->orderByDesc('created_at');

        if ($request->query('status')) {
            $query->where('status', $request->query('status'));
        }

        $orders = $query->paginate(30);
        return response()->json(['success' => true, 'data' => $orders, 'message' => 'Órdenes obtenidas correctamente']);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'items'             => 'required|array|min:1',
            'items.*.dish_id'   => 'nullable|integer',
            'items.*.name_snapshot' => 'required_without:items.*.dish_id|string',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.quantity'  => 'required|integer|min:1',
            'items.*.notes'     => 'nullable|string|max:255',
            'tip'               => 'nullable|numeric|min:0',
            'notes'             => 'nullable|string|max:500',
            'customer_name'     => 'nullable|string|max:255',
            'table_name'        => 'nullable|string|max:100',
            'order_type'        => 'nullable|in:dine_in,takeout,delivery',
            'discount_code'     => 'nullable|string|max:50',
            'discount_percent'  => 'nullable|numeric|min:0|max:100',
        ]);

        try {
            $order = $this->orderService->createOrder($request->all(), Auth::id() ?? 0);
            return response()->json(['success' => true, 'data' => $order, 'message' => 'Orden creada correctamente'], 201);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function show(int $id): JsonResponse
    {
        $order = PosOrder::with(['items', 'payments', 'user', 'cancelledBy'])->findOrFail($id);
        return response()->json(['success' => true, 'data' => $order, 'message' => 'Orden obtenida correctamente']);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $order = PosOrder::findOrFail($id);

        $request->validate([
            'customer_name'          => 'nullable|string|max:255',
            'table_name'             => 'nullable|string|max:100',
            'order_type'             => 'nullable|in:dine_in,takeout,delivery',
            'notes'                  => 'nullable|string|max:500',
            'tip'                    => 'nullable|numeric|min:0',
            'tax'                    => 'nullable|numeric|min:0',
            'items'                  => 'nullable|array|min:1',
            'items.*.dish_id'        => 'nullable|integer',
            'items.*.name_snapshot'  => 'required_without:items.*.dish_id|string',
            'items.*.unit_price'     => 'required_with:items|numeric|min:0',
            'items.*.quantity'       => 'required_with:items|integer|min:1',
            'items.*.notes'          => 'nullable|string|max:255',
            'discount_code'          => 'nullable|string|max:50',
            'discount_percent'       => 'nullable|numeric|min:0|max:100',
        ]);

        try {
            $order = $this->orderService->updateOrder($order, $request->all());
            return response()->json(['success' => true, 'data' => $order, 'message' => 'Orden actualizada correctamente']);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        if (Auth::user()?->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Solo un administrador puede eliminar órdenes.',
            ], 403);
        }

        $order = PosOrder::findOrFail($id);

        $request->validate(['reason' => 'nullable|string|max:255']);

        try {
            $this->orderService->cancelOrder($order, Auth::id(), $request->input('reason'));
            return response()->json(['success' => true, 'data' => null, 'message' => 'Orden cancelada correctamente']);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function addItem(Request $request, int $id): JsonResponse
    {
        $order = PosOrder::findOrFail($id);

        $request->validate([
            'dish_id'       => 'nullable|integer',
            'name_snapshot' => 'nullable|string',
            'unit_price'    => 'required|numeric|min:0',
            'quantity'      => 'required|integer|min:1',
            'notes'         => 'nullable|string|max:255',
        ]);

        try {
            $item = $this->orderService->addItemToOrder($order, $request->all());
            return response()->json(['success' => true, 'data' => $item, 'message' => 'Producto agregado a la orden'], 201);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function updateItem(Request $request, int $id, int $itemId): JsonResponse
    {
        $order = PosOrder::findOrFail($id);
        $item  = PosOrderItem::where('pos_order_id', $id)->findOrFail($itemId);

        $request->validate(['quantity' => 'required|integer|min:1']);

        try {
            $updated = $this->orderService->updateItemQuantity($order, $item, $request->quantity);
            return response()->json(['success' => true, 'data' => $updated, 'message' => 'Cantidad actualizada correctamente']);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function removeItem(int $id, int $itemId): JsonResponse
    {
        $order = PosOrder::findOrFail($id);
        $item  = PosOrderItem::where('pos_order_id', $id)->findOrFail($itemId);

        try {
            $this->orderService->removeItem($order, $item);
            return response()->json(['success' => true, 'data' => null, 'message' => 'Producto eliminado de la orden']);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function activeCount(): JsonResponse
    {
        $count = PosOrder::where('status', 'open')->count();
        return response()->json(['active_orders' => $count]);
    }

    public function pay(Request $request, int $id): JsonResponse
    {
        $order = PosOrder::findOrFail($id);

        $request->validate([
            'payments'          => 'required|array|min:1',
            'payments.*.method' => 'required|in:cash,card,transfer,credit',
            'payments.*.amount' => 'required|numeric|min:0.01',
        ]);

        $hasCredit = collect($request->payments)->contains('method', 'credit');

        if ($hasCredit) {
            $request->validate([
                'customer_id' => 'required|integer|exists:customers,id',
            ]);
        }

        $activeShift = $this->shiftService->getActiveShift();

        if (!$activeShift) {
            return response()->json([
                'success' => false,
                'message' => 'No hay turno abierto. Abre un turno antes de cobrar.',
            ], 422);
        }

        if ($hasCredit) {
            $customer     = Customer::findOrFail($request->customer_id);
            $creditAmount = collect($request->payments)
                ->where('method', 'credit')
                ->sum(fn($p) => (float) ($p['amount'] ?? 0));

            if ($customer->credit_limit !== null && ((float) $customer->balance + $creditAmount) > (float) $customer->credit_limit) {
                return response()->json([
                    'success' => false,
                    'message' => 'El cliente excede su límite de crédito.',
                ], 422);
            }
        }

        try {
            $userId = Auth::id() ?? 0;
            $result = $this->paymentService->payMultiple($order, $request->payments, $userId, $request->customer_id);

            // Register a cash movement for every payment (cash affects expected_cash; card/transfer/credit are for reporting only)
            foreach ($result['payments'] as $payment) {
                $this->cashMovementService->registerSalePayment($activeShift, $order, $payment, $userId);
            }

            return response()->json([
                'success' => true,
                'data'    => array_merge($result, ['order' => $order->fresh()]),
                'message' => 'Pago registrado correctamente',
            ]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function ticket(int $id): Response
    {
        $order = PosOrder::with(['items', 'payments', 'user'])->findOrFail($id);

        $orderTypeLabels = [
            'dine_in'  => 'Comer aquí',
            'takeout'  => 'Para llevar',
            'delivery' => 'A domicilio',
        ];

        $paymentMethodLabels = [
            'cash'     => 'Efectivo',
            'card'     => 'Tarjeta',
            'transfer' => 'Transferencia',
            'credit'   => 'Crédito',
        ];

        $entered        = $order->payments->sum('amount');
        $totalBeforeTip = round((float) $order->total - (float) $order->tip, 2);
        $change         = max(0, round($entered - (float) $order->total, 2));

        $pdf = Pdf::loadView('pos.ticket', [
            'order'                => $order,
            'businessName'         => config('app.name'),
            'logoDataUri'          => $this->logoDataUri(),
            'orderTypeLabel'       => $orderTypeLabels[$order->order_type] ?? $order->order_type,
            'paymentMethodLabels'  => $paymentMethodLabels,
            'totalBeforeTip'       => $totalBeforeTip,
            'change'               => $change,
        ]);

        $pdf->setPaper([0, 0, 226.77, 1600]);

        return $pdf->stream("ticket-{$order->order_number}.pdf");
    }

    public function comanda(Request $request, int $id): Response
    {
        $order = PosOrder::with(['items', 'user'])->findOrFail($id);

        $orderTypeLabels = [
            'dine_in'  => 'Comer aquí',
            'takeout'  => 'Para llevar',
            'delivery' => 'A domicilio',
        ];

        // Reimpresión completa (p. ej. se atascó el papel): muestra todo, sin marcar nada
        // como enviado ni afectar el rastreo de lo que ya se mandó a cocina.
        $full = $request->boolean('full');

        $lines = [];
        foreach ($order->items as $item) {
            $pendingQty = $full ? $item->quantity : ($item->quantity - $item->sent_to_kitchen_qty);
            if ($pendingQty <= 0) {
                continue;
            }
            $lines[] = [
                'name'          => $item->name_snapshot,
                'quantity'      => $pendingQty,
                'notes'         => $item->notes,
                'is_addition'   => !$full && $item->sent_to_kitchen_qty > 0,
            ];
        }

        $pdf = Pdf::loadView('pos.comanda', [
            'order'          => $order,
            'orderTypeLabel' => $orderTypeLabels[$order->order_type] ?? $order->order_type,
            'lines'          => $lines,
            'isReprint'      => $full,
        ]);

        $pdf->setPaper([0, 0, 226.77, 1600]);

        if (!$full) {
            foreach ($order->items as $item) {
                if ($item->sent_to_kitchen_qty < $item->quantity) {
                    $item->update(['sent_to_kitchen_qty' => $item->quantity]);
                }
            }
        }

        return $pdf->stream("comanda-{$order->order_number}.pdf");
    }

    private function logoDataUri(): ?string
    {
        $path = resource_path('images/logo-mono.png');

        if (!file_exists($path)) {
            return null;
        }

        return 'data:image/png;base64,' . base64_encode(file_get_contents($path));
    }
}
