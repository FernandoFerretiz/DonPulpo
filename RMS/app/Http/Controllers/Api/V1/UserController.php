<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(): JsonResponse
    {
        $users = User::orderBy('name')->get();
        return response()->json(['success' => true, 'data' => $users, 'message' => 'Usuarios obtenidos correctamente']);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role'     => ['required', Rule::in(User::ROLES)],
            'status'   => ['required', Rule::in(User::STATUSES)],
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $user = User::create($validated);

        return response()->json(['success' => true, 'data' => $user, 'message' => 'Usuario creado correctamente'], 201);
    }

    public function show(int $id): JsonResponse
    {
        $user = User::findOrFail($id);
        return response()->json(['success' => true, 'data' => $user, 'message' => 'Usuario obtenido correctamente']);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name'   => 'sometimes|string|max:255',
            'email'  => ['sometimes', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'role'   => ['sometimes', Rule::in(User::ROLES)],
            'status' => ['sometimes', Rule::in(User::STATUSES)],
        ]);

        if ($request->filled('password')) {
            $request->validate(['password' => 'string|min:8']);
            $validated['password'] = Hash::make($request->password);
        }

        $user->update($validated);

        return response()->json(['success' => true, 'data' => $user->fresh(), 'message' => 'Usuario actualizado correctamente']);
    }

    public function destroy(int $id): JsonResponse
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['success' => true, 'data' => null, 'message' => 'Usuario eliminado correctamente']);
    }
}
