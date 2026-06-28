<?php

namespace App\Http\Controllers;

use App\Models\Dish;
use App\Models\DishCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
            'image'            => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
            'price'            => 'required|numeric|min:0',
            'status'           => ['required', Rule::in(Dish::STATUSES)],
        ]);

        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('dishes', 'public');
        }
        unset($validated['image']);

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
            'image'            => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
            'remove_image'     => 'nullable|in:1',
            'price'            => 'required|numeric|min:0',
            'status'           => ['required', Rule::in(Dish::STATUSES)],
        ]);

        if ($request->hasFile('image')) {
            if ($dish->image_path) {
                Storage::disk('public')->delete($dish->image_path);
            }
            $validated['image_path'] = $request->file('image')->store('dishes', 'public');
        } elseif ($request->input('remove_image') === '1') {
            if ($dish->image_path) {
                Storage::disk('public')->delete($dish->image_path);
            }
            $validated['image_path'] = null;
        }

        unset($validated['image'], $validated['remove_image']);
        $dish->update($validated);

        return redirect()->route('dishes.index')->with('success', 'Platillo actualizado correctamente.');
    }

    public function destroy(Dish $dish): RedirectResponse
    {
        if ($dish->image_path) {
            Storage::disk('public')->delete($dish->image_path);
        }
        $dish->delete();
        return redirect()->route('dishes.index')->with('success', 'Platillo eliminado correctamente.');
    }
}
