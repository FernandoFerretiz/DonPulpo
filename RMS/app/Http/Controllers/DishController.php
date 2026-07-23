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
    public function index(Request $request): View
    {
        $search = $request->get('search');
        $dishes = Dish::with('category')
            ->when($search, fn($q) => $q->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->paginate(25)
            ->withQueryString();
        return view('dishes.index', compact('dishes', 'search'));
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
            $this->syncImageToPos($validated['image_path']);
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
        $dish->update($validated);

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
