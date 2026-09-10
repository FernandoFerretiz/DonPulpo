<?php

namespace App\Http\Controllers\Api\V1\Cobro;

use App\Http\Controllers\Controller;
use App\Models\PosTable;
use Illuminate\Http\JsonResponse;

class TableController extends Controller
{
    /**
     * GET /api/v1/tables — catálogo de mesas para el selector de la app.
     * No implica exclusividad: una mesa puede tener varias órdenes activas.
     */
    public function index(): JsonResponse
    {
        $tables = PosTable::where('active', true)
            ->orderBy('display_order')
            ->orderBy('name')
            ->get(['id', 'name', 'capacity']);

        return response()->json(['success' => true, 'data' => $tables]);
    }
}
