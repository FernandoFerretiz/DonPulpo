<?php

namespace App\Http\Controllers;

use App\Models\InventoryProduct;
use App\Models\UnitOfMeasure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UnitOfMeasureController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->get('search');
        $units = UnitOfMeasure::with('baseUnit')
            ->when($search, fn($q) => $q->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->paginate(25)
            ->withQueryString();
        return view('inventory.units.index', compact('units', 'search'));
    }

    public function create(): View
    {
        return view('inventory.units.create', ['units' => UnitOfMeasure::orderBy('name')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'              => 'required|string|max:255',
            'abbreviation'      => 'required|string|max:20',
            'base_unit_id'      => 'nullable|exists:units_of_measure,id',
            'conversion_factor' => 'required|numeric|min:0.0001',
        ]);

        UnitOfMeasure::create($validated);

        return redirect()->route('inventory.units.index')->with('success', 'Unidad de medida creada correctamente.');
    }

    public function edit(UnitOfMeasure $unit): View
    {
        return view('inventory.units.edit', [
            'unit'  => $unit,
            'units' => UnitOfMeasure::where('id', '!=', $unit->id)->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, UnitOfMeasure $unit): RedirectResponse
    {
        $validated = $request->validate([
            'name'              => 'required|string|max:255',
            'abbreviation'      => 'required|string|max:20',
            'base_unit_id'      => ['nullable', 'exists:units_of_measure,id', Rule::notIn([$unit->id])],
            'conversion_factor' => 'required|numeric|min:0.0001',
        ]);

        $unit->update($validated);

        return redirect()->route('inventory.units.index')->with('success', 'Unidad de medida actualizada correctamente.');
    }

    public function destroy(UnitOfMeasure $unit): RedirectResponse
    {
        if (InventoryProduct::where('unit_of_measure_id', $unit->id)->exists()) {
            return back()->with('error', 'No se puede eliminar: hay productos usando esta unidad.');
        }

        $unit->delete();
        return redirect()->route('inventory.units.index')->with('success', 'Unidad de medida eliminada correctamente.');
    }
}
