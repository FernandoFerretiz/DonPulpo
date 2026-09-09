<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::orderBy('name')->paginate(20);
        return view('users.index', compact('users'));
    }

    public function create(): View
    {
        return view('users.create', ['roles' => User::ROLES, 'statuses' => User::STATUSES]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'email'            => 'required|email|unique:users,email',
            // La contraseña es opcional: hay empleados (ej. meseros/cajeros)
            // que solo usan el POS con PIN y nunca entran al RMS.
            'password'         => 'nullable|string|min:8|confirmed',
            'employee_number'  => 'nullable|string|max:50|unique:users,employee_number',
            'pin'              => 'nullable|digits:4',
            'role'             => ['required', Rule::in(User::ROLES)],
            'status'           => ['required', Rule::in(User::STATUSES)],
        ]);

        $validated['password'] = $validated['password'] ? Hash::make($validated['password']) : null;
        $validated['pin'] = $validated['pin'] ? Hash::make($validated['pin']) : null;
        User::create($validated);

        return redirect()->route('users.index')->with('success', 'Usuario creado correctamente.');
    }

    public function edit(User $user): View
    {
        return view('users.edit', [
            'user'     => $user,
            'roles'    => User::ROLES,
            'statuses' => User::STATUSES,
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'email'           => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'employee_number' => ['nullable', 'string', 'max:50', Rule::unique('users', 'employee_number')->ignore($user->id)],
            'role'            => ['required', Rule::in(User::ROLES)],
            'status'          => ['required', Rule::in(User::STATUSES)],
        ]);

        if ($request->filled('password')) {
            $request->validate(['password' => 'string|min:8|confirmed']);
            $validated['password'] = Hash::make($request->password);
        }

        if ($request->filled('pin')) {
            $request->validate(['pin' => 'digits:4']);
            $validated['pin'] = Hash::make($request->pin);
        }

        $user->update($validated);

        return redirect()->route('users.index')->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'Usuario eliminado correctamente.');
    }
}
