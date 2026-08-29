<?php

namespace App\Http\Controllers;

use App\Models\ModifierGroup;
use App\Models\ModifierOption;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ModifierGroupController extends Controller
{
    public function index(): View
    {
        $groups = ModifierGroup::with('options')->orderBy('name')->get();
        return view('modifier-groups.index', compact('groups'));
    }

    public function create(): View
    {
        return view('modifier-groups.create', [
            'selectionTypes' => ModifierGroup::SELECTION_TYPES,
            'pricingModes'   => ModifierGroup::PRICING_MODES,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'               => 'required|string|max:255',
            'selection_type'     => ['required', Rule::in(ModifierGroup::SELECTION_TYPES)],
            'pricing_mode'       => ['required', Rule::in(ModifierGroup::PRICING_MODES)],
            'required'           => 'nullable|boolean',
            'options'            => 'required|array|min:1',
            'options.*.name'         => 'required|string|max:255',
        ]);

        $group = ModifierGroup::create([
            'name'           => $validated['name'],
            'selection_type' => $validated['selection_type'],
            'pricing_mode'   => $validated['pricing_mode'],
            'required'       => $request->boolean('required'),
        ]);

        foreach ($validated['options'] as $i => $option) {
            $group->options()->create([
                'name'          => $option['name'],
                'display_order' => $i,
            ]);
        }

        return redirect()->route('modifier-groups.index')->with('success', 'Grupo de modificadores creado correctamente.');
    }

    public function edit(ModifierGroup $modifierGroup): View
    {
        return view('modifier-groups.edit', [
            'group'          => $modifierGroup->load('options'),
            'selectionTypes' => ModifierGroup::SELECTION_TYPES,
            'pricingModes'   => ModifierGroup::PRICING_MODES,
        ]);
    }

    public function update(Request $request, ModifierGroup $modifierGroup): RedirectResponse
    {
        $validated = $request->validate([
            'name'                   => 'required|string|max:255',
            'selection_type'         => ['required', Rule::in(ModifierGroup::SELECTION_TYPES)],
            'pricing_mode'           => ['required', Rule::in(ModifierGroup::PRICING_MODES)],
            'required'               => 'nullable|boolean',
            'options'                => 'required|array|min:1',
            'options.*.id'           => 'nullable|integer|exists:modifier_options,id',
            'options.*.name'         => 'required|string|max:255',
        ]);

        $modifierGroup->update([
            'name'           => $validated['name'],
            'selection_type' => $validated['selection_type'],
            'pricing_mode'   => $validated['pricing_mode'],
            'required'       => $request->boolean('required'),
        ]);

        $keptIds = [];
        foreach ($validated['options'] as $i => $option) {
            $saved = $modifierGroup->options()->updateOrCreate(
                ['id' => $option['id'] ?? null],
                [
                    'name'          => $option['name'],
                    'display_order' => $i,
                ]
            );
            $keptIds[] = $saved->id;
        }
        $modifierGroup->options()->whereNotIn('id', $keptIds)->delete();

        return redirect()->route('modifier-groups.index')->with('success', 'Grupo de modificadores actualizado correctamente.');
    }

    public function destroy(ModifierGroup $modifierGroup): RedirectResponse
    {
        $modifierGroup->delete();
        return redirect()->route('modifier-groups.index')->with('success', 'Grupo de modificadores eliminado correctamente.');
    }
}
