<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\PosOrder;
use App\Models\PosOrderItem;
use App\Services\OrderService;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function __construct(
        private OrderService $orderService,
        private PaymentService $paymentService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $query = PosOrder::with(['items', 'user'])->orderByDesc('created_at');

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
        $order = PosOrder::with(['items', 'payments', 'user'])->findOrFail($id);
        return response()->json(['success' => true, 'data' => $order, 'message' => 'Orden obtenida correctamente']);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $order = PosOrder::findOrFail($id);

        $request->validate([
            'customer_name' => 'nullable|string|max:255',
            'table_name'    => 'nullable|string|max:100',
            'order_type'    => 'nullable|in:dine_in,takeout,delivery',
            'notes'         => 'nullable|string|max:500',
            'tip'           => 'nullable|numeric|min:0',
        ]);

        try {
            if ($request->has('tip')) {
                $order->update(['tip' => $request->tip]);
                $this->orderService->recalculateTotals($order);
            }

            $order->update($request->only(['customer_name', 'table_name', 'order_type', 'notes']));

            return response()->json(['success' => true, 'data' => $order->fresh(['items']), 'message' => 'Orden actualizada correctamente']);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        $order = PosOrder::findOrFail($id);

        try {
            $this->orderService->cancelOrder($order);
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
            'payments'            => 'required|array|min:1',
            'payments.*.method'   => 'required|in:cash,card,transfer',
            'payments.*.amount'   => 'required|numeric|min:0.01',
        ]);

        try {
            $result = $this->paymentService->payMultiple($order, $request->payments, Auth::id() ?? 0);
            return response()->json([
                'success' => true,
                'data'    => array_merge($result, ['order' => $order->fresh()]),
                'message' => 'Pago registrado correctamente',
            ]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }
}
