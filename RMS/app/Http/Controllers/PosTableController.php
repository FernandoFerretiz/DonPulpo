<?php

namespace App\Http\Controllers;

use App\Models\PosTable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PosTableController extends Controller
{
    public function index(): View
    {
        $tables = PosTable::orderBy('display_order')->orderBy('name')->get();
        return view('tables.index', compact('tables'));
    }

    public function create(): View
    {
        return view('tables.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:50',
            'capacity'      => 'required|integer|min:1|max:255',
            'display_order' => 'nullable|integer|min:0',
            'active'        => 'nullable|in:1',
        ]);

        PosTable::create([
            'name'          => $validated['name'],
            'capacity'      => $validated['capacity'],
            'display_order' => $validated['display_order'] ?? 0,
            'active'        => $request->input('active') === '1',
        ]);

        return redirect()->route('tables.index')->with('success', 'Mesa creada correctamente.');
    }

    public function edit(PosTable $table): View
    {
        return view('tables.edit', ['table' => $table]);
    }

    public function update(Request $request, PosTable $table): RedirectResponse
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:50',
            'capacity'      => 'required|integer|min:1|max:255',
            'display_order' => 'nullable|integer|min:0',
            'active'        => 'nullable|in:1',
        ]);

        $table->update([
            'name'          => $validated['name'],
            'capacity'      => $validated['capacity'],
            'display_order' => $validated['display_order'] ?? 0,
            'active'        => $request->input('active') === '1',
        ]);

        return redirect()->route('tables.index')->with('success', 'Mesa actualizada correctamente.');
    }

    public function destroy(PosTable $table): RedirectResponse
    {
        $table->delete();
        return redirect()->route('tables.index')->with('success', 'Mesa eliminada correctamente.');
    }
}
