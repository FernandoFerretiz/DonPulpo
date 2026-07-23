<?php

namespace App\Http\Controllers;

use App\Models\PosShift;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShiftController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status');

        $query = PosShift::with('user')->orderByDesc('opened_at');

        if ($status) {
            $query->where('status', $status);
        }

        $shifts     = $query->paginate(20)->withQueryString();
        $statusTabs = [
            ''          => 'Todos',
            'open'      => 'Abiertos',
            'closed'    => 'Cerrados',
            'cancelled' => 'Cancelados',
        ];

        return view('shifts.index', compact('shifts', 'status', 'statusTabs'));
    }

    public function show(PosShift $shift): View
    {
        $shift->load([
            'user',
            'cashMovements' => fn ($q) => $q->orderBy('created_at'),
            'cashMovements.user',
            'cashMovements.reference',
            'cashMovements.reference.order',
            'pettyCashVouchers.category',
            'pettyCashVouchers.requestedBy',
        ]);

        return view('shifts.show', compact('shift'));
    }
}
