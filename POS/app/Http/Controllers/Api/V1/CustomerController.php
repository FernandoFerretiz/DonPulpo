<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Customer::query();

        if ($search = $request->get('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $customers = $query->orderBy('name')->limit(100)->get();

        return response()->json(['success' => true, 'data' => $customers]);
    }

    public function store(Request $request): JsonResponse
    {
        if (Auth::user()?->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Solo un administrador puede crear clientes.',
            ], 403);
        }

        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'phone'        => 'nullable|string|max:30',
            'credit_limit' => 'nullable|numeric|min:0',
        ]);

        $customer = Customer::create($data);

        return response()->json(['success' => true, 'data' => $customer], 201);
    }
}
