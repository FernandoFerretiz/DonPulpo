<?php

namespace App\Http\Controllers;

use App\Models\Dish;
use App\Models\DishCategory;
use App\Models\ModifierGroup;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DishController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->get('search');
        $dishes = Dish::with('category')
            ->when($search, fn($q) => $q->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->paginate(25)
            ->withQueryString();

        if ($request->boolean('partial')) {
            return view('dishes._table', compact('dishes', 'search'));
        }

        return view('dishes.index', compact('dishes', 'search'));
    }

    public function create(): View
    {
        return view('dishes.create', [
            'categories'     => DishCategory::orderBy('display_order')->get(),
            'statuses'       => Dish::STATUSES,
            'modifierGroups' => ModifierGroup::with('options')->orderBy('name')->get(),
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
            'modifier_group_ids'   => 'nullable|array',
            'modifier_group_ids.*' => 'integer|exists:modifier_groups,id',
            'modifier_prices'      => 'nullable|array',
            'modifier_prices.*'    => 'nullable|numeric',
        ]);

        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('dishes', 'public');
            $this->syncImageToPos($validated['image_path']);
        }
        unset($validated['image']);
        $modifierGroupIds = $validated['modifier_group_ids'] ?? [];
        $modifierPrices   = $validated['modifier_prices'] ?? [];
        unset($validated['modifier_group_ids'], $validated['modifier_prices']);

        $dish = Dish::create($validated);
        $this->syncModifiers($dish, $modifierGroupIds, $modifierPrices);

        return redirect()->route('dishes.index')->with('success', 'Platillo creado correctamente.');
    }

    public function edit(Request $request, Dish $dish): View
    {
        $data = [
            'dish'           => $dish,
            'categories'     => DishCategory::orderBy('display_order')->get(),
            'statuses'       => Dish::STATUSES,
            'modifierGroups' => ModifierGroup::with('options')->orderBy('name')->get(),
            'selectedGroupIds' => $dish->modifierGroups()->pluck('modifier_groups.id')->all(),
            'optionPrices'   => $dish->modifierOptions()->pluck('dish_modifier_options.price_delta', 'modifier_options.id'),
        ];

        if ($request->boolean('modal')) {
            return view('dishes._form', $data);
        }

        return view('dishes.edit', $data);
    }

    public function update(Request $request, Dish $dish): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'dish_category_id' => 'nullable|exists:dish_categories,id',
            'name'             => 'required|string|max:255',
            'description'      => 'nullable|string',
            'image'            => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
            'remove_image'     => 'nullable|in:1',
            'price'            => 'required|numeric|min:0',
            'status'           => ['required', Rule::in(Dish::STATUSES)],
            'modifier_group_ids'   => 'nullable|array',
            'modifier_group_ids.*' => 'integer|exists:modifier_groups,id',
            'modifier_prices'      => 'nullable|array',
            'modifier_prices.*'    => 'nullable|numeric',
        ]);

        if ($request->hasFile('image')) {
            if ($dish->image_path) {
                Storage::disk('public')->delete($dish->image_path);
                $this->removeImageFromPos($dish->image_path);
            }
            $validated['image_path'] = $request->file('image')->store('dishes', 'public');
            $this->syncImageToPos($validated['image_path']);
        } elseif ($request->input('remove_image') === '1') {
            if ($dish->image_path) {
                Storage::disk('public')->delete($dish->image_path);
                $this->removeImageFromPos($dish->image_path);
            }
            $validated['image_path'] = null;
        }

        unset($validated['image'], $validated['remove_image']);
        $modifierGroupIds = $validated['modifier_group_ids'] ?? [];
        $modifierPrices   = $validated['modifier_prices'] ?? [];
        unset($validated['modifier_group_ids'], $validated['modifier_prices']);

        $dish->update($validated);
        $this->syncModifiers($dish, $modifierGroupIds, $modifierPrices);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Platillo actualizado correctamente.']);
        }

        return redirect()->route('dishes.index')->with('success', 'Platillo actualizado correctamente.');
    }

    public function destroy(Dish $dish): RedirectResponse
    {
        if ($dish->image_path) {
            Storage::disk('public')->delete($dish->image_path);
            $this->removeImageFromPos($dish->image_path);
        }
        $dish->delete();
        return redirect()->route('dishes.index')->with('success', 'Platillo eliminado correctamente.');
    }

    /**
     * Enables the given modifier groups on the dish and sets the price of each of
     * their options for this specific dish (same group can have a different price
     * per dish, e.g. "Tamaño MED" costs different on each caldo).
     */
    private function syncModifiers(Dish $dish, array $modifierGroupIds, array $modifierPrices): void
    {
        $dish->modifierGroups()->sync($modifierGroupIds);

        $optionIds = \App\Models\ModifierOption::whereIn('modifier_group_id', $modifierGroupIds)->pluck('id');
        $syncData  = $optionIds->mapWithKeys(fn($id) => [
            $id => ['price_delta' => $modifierPrices[$id] ?? 0],
        ])->all();

        $dish->modifierOptions()->sync($syncData);
    }

    /**
     * POS is a sibling Laravel app that reads dish images from its own
     * storage disk. It doesn't share a filesystem/symlink with RMS in
     * every environment, so the file is copied over on write.
     */
    private function syncImageToPos(string $relativePath): void
    {
        $target = base_path('../POS/storage/app/public/' . $relativePath);
        $source = Storage::disk('public')->path($relativePath);

        if (! is_dir(dirname($target))) {
            mkdir(dirname($target), 0755, true);
        }

        @copy($source, $target);
    }

    private function removeImageFromPos(string $relativePath): void
    {
        $target = base_path('../POS/storage/app/public/' . $relativePath);
        if (is_file($target)) {
            @unlink($target);
        }
    }
}
