<?php

namespace App\Http\Controllers\Api\V1\Cobro;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Login por PIN para la app nativa (Flutter): devuelve un token Sanctum
     * en vez de una sesión-cookie, ya que la app no puede depender de cookies.
     */
    public function pinLogin(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'employee_number' => 'required|string',
            'pin'             => 'required|string',
        ]);

        $user = User::where('employee_number', $credentials['employee_number'])->first();

        if (!$user || $user->status !== 'active' || !in_array($user->role, User::POS_ROLES)) {
            return response()->json([
                'success' => false,
                'message' => 'Credenciales inválidas o sin permisos de acceso.',
            ], 401);
        }

        if (!$user->pin || !Hash::check($credentials['pin'], $user->pin)) {
            return response()->json([
                'success' => false,
                'message' => 'PIN incorrecto.',
            ], 401);
        }

        $token = $user->createToken('pos-cobro')->plainTextToken;

        return response()->json([
            'success' => true,
            'data'    => [
                'token' => $token,
                'user'  => $user->only(['id', 'name', 'email', 'employee_number', 'role']),
            ],
            'message' => 'Sesión iniciada correctamente.',
        ]);
    }

    /**
     * Desbloqueo por PIN de la pantalla de bloqueo por inactividad: valida el PIN
     * del usuario ya autenticado por token, sin crear una sesión/token nuevo.
     */
    public function pinVerify(Request $request): JsonResponse
    {
        $data = $request->validate([
            'pin' => 'required|string',
        ]);

        $user = $request->user();

        if (!$user->pin || !Hash::check($data['pin'], $user->pin)) {
            return response()->json([
                'success' => false,
                'message' => 'PIN incorrecto.',
            ], 401);
        }

        return response()->json([
            'success' => true,
            'data'    => null,
            'message' => 'PIN verificado.',
        ]);
    }

    /**
     * Cierre de sesión de la app nativa: revoca el token Sanctum actual.
     */
    public function tokenLogout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['success' => true, 'data' => null, 'message' => 'Sesión cerrada.']);
    }

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
