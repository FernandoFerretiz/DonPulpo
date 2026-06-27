<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\PosOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function summary(): JsonResponse
    {
        $today = Carbon::today();

        $salesDay = PosOrder::where('status', 'paid')
            ->whereDate('paid_at', $today)
            ->sum('total');

        $openOrders = PosOrder::where('status', 'open')->count();

        $paidOrders = PosOrder::where('status', 'paid')
            ->whereDate('paid_at', $today)
            ->count();

        $averageTicket = $paidOrders > 0
            ? round($salesDay / $paidOrders, 2)
            : 0;

        return response()->json([
            'success' => true,
            'data'    => [
                'sales_today'    => number_format($salesDay, 2, '.', ''),
                'open_orders'    => $openOrders,
                'paid_orders'    => $paidOrders,
                'average_ticket' => number_format($averageTicket, 2, '.', ''),
                'date'           => $today->toDateString(),
            ],
            'message' => 'Resumen obtenido correctamente',
        ]);
    }
}
