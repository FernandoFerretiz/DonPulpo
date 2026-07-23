<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\DiscountCode;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DiscountCodeController extends Controller
{
    public function validateCode(Request $request): JsonResponse
    {
        $request->validate(['code' => 'required|string|max:50']);

        $code     = strtoupper(trim($request->code));
        $discount = DiscountCode::where('code', $code)->where('status', 'active')->first();

        if (!$discount) {
            return response()->json(['success' => false, 'message' => 'Código de descuento inválido o inactivo.'], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'code'             => $discount->code,
                'percentage'       => (float) $discount->percentage,
                'beneficiary_name' => $discount->beneficiary_name,
            ],
        ]);
    }
}
