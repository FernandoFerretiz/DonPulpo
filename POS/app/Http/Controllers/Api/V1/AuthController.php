<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || $user->status !== 'active' || !in_array($user->role, User::POS_ROLES)) {
            return response()->json([
                'success' => false,
                'message' => 'Credenciales inválidas o sin permisos de acceso.',
            ], 401);
        }

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Credenciales incorrectas.',
            ], 401);
        }

        $request->session()->regenerate();

        return response()->json([
            'success' => true,
            'data'    => $user->only(['id', 'name', 'email', 'role']),
            'message' => 'Sesión iniciada correctamente.',
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'No autenticado.'], 401);
        }

        return response()->json([
            'success' => true,
            'data'    => Auth::user()->only(['id', 'name', 'email', 'role']),
            'message' => 'Usuario autenticado.',
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['success' => true, 'data' => null, 'message' => 'Sesión cerrada.']);
    }
}
