<?php

namespace App\Http\Controllers;

use App\Models\Dish;
use App\Models\DishCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DishController extends Controller
{
    public function index(): View
    {
        $dishes = Dish::with('category')->orderBy('name')->paginate(25);
        return view('dishes.index', compact('dishes'));
    }

    public function create(): View
    {
        return view('dishes.create', [
            'categories' => DishCategory::orderBy('display_order')->get(),
            'statuses'   => Dish::STATUSES,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'dish_category_id' => 'nullable|exists:dish_categories,id',
            'name'             => 'required|string|max:255',
            'description'      => 'nullable|string',
            'image_path'       => 'nullable|string|max:500',
            'price'            => 'required|numeric|min:0',
            'status'           => ['required', Rule::in(Dish::STATUSES)],
        ]);

        Dish::create($validated);

        return redirect()->route('dishes.index')->with('success', 'Platillo creado correctamente.');
    }

    public function edit(Dish $dish): View
    {
        return view('dishes.edit', [
            'dish'       => $dish,
            'categories' => DishCategory::orderBy('display_order')->get(),
            'statuses'   => Dish::STATUSES,
        ]);
    }

    public function update(Request $request, Dish $dish): RedirectResponse
    {
        $validated = $request->validate([
            'dish_category_id' => 'nullable|exists:dish_categories,id',
            'name'             => 'required|string|max:255',
            'description'      => 'nullable|string',
            'image_path'       => 'nullable|string|max:500',
            'price'            => 'required|numeric|min:0',
            'status'           => ['required', Rule::in(Dish::STATUSES)],
        ]);

        $dish->update($validated);

        return redirect()->route('dishes.index')->with('success', 'Platillo actualizado correctamente.');
    }

    public function destroy(Dish $dish): RedirectResponse
    {
        $dish->delete();
        return redirect()->route('dishes.index')->with('success', 'Platillo eliminado correctamente.');
    }
}
