<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SupplierController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->get('search');
        $suppliers = Supplier::withCount('products')
            ->when($search, fn($q) => $q->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->paginate(25)
            ->withQueryString();
        return view('inventory.suppliers.index', compact('suppliers', 'search'));
    }

    public function create(): View
    {
        return view('inventory.suppliers.create', ['statuses' => Supplier::STATUSES]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'contact_name' => 'nullable|string|max:255',
            'phone'        => 'nullable|string|max:50',
            'email'        => 'nullable|email|max:255',
            'address'      => 'nullable|string|max:255',
            'status'       => ['required', Rule::in(Supplier::STATUSES)],
        ]);

        Supplier::create($validated);

        return redirect()->route('inventory.suppliers.index')->with('success', 'Proveedor creado correctamente.');
    }

    public function edit(Supplier $supplier): View
    {
        return view('inventory.suppliers.edit', [
            'supplier' => $supplier,
            'statuses' => Supplier::STATUSES,
        ]);
    }

    public function update(Request $request, Supplier $supplier): RedirectResponse
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'contact_name' => 'nullable|string|max:255',
            'phone'        => 'nullable|string|max:50',
            'email'        => 'nullable|email|max:255',
            'address'      => 'nullable|string|max:255',
            'status'       => ['required', Rule::in(Supplier::STATUSES)],
        ]);

        $supplier->update($validated);

        return redirect()->route('inventory.suppliers.index')->with('success', 'Proveedor actualizado correctamente.');
    }

    public function destroy(Supplier $supplier): RedirectResponse
    {
        $supplier->delete();
        return redirect()->route('inventory.suppliers.index')->with('success', 'Proveedor eliminado correctamente.');
    }
}
