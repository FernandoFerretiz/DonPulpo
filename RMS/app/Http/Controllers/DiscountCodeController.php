<?php

namespace App\Http\Controllers;

use App\Models\DiscountCode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DiscountCodeController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->get('search');
        $codes = DiscountCode::query()
            ->when($search, fn($q) => $q->where('code', 'like', "%{$search}%")
                ->orWhere('beneficiary_name', 'like', "%{$search}%"))
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();
        return view('discount-codes.index', compact('codes', 'search'));
    }

    public function create(): View
    {
        return view('discount-codes.create', ['statuses' => DiscountCode::STATUSES]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code'             => 'required|string|max:50|unique:discount_codes,code',
            'percentage'       => 'required|numeric|min:0.01|max:100',
            'beneficiary_name' => 'required|string|max:255',
            'status'           => ['required', Rule::in(DiscountCode::STATUSES)],
        ]);

        $validated['code'] = Str::upper(trim($validated['code']));

        DiscountCode::create($validated);

        return redirect()->route('discount-codes.index')->with('success', 'Código de descuento creado correctamente.');
    }

    public function edit(DiscountCode $discountCode): View
    {
        return view('discount-codes.edit', [
            'discountCode' => $discountCode,
            'statuses'     => DiscountCode::STATUSES,
        ]);
    }

    public function update(Request $request, DiscountCode $discountCode): RedirectResponse
    {
        $validated = $request->validate([
            'code'             => ['required', 'string', 'max:50', Rule::unique('discount_codes', 'code')->ignore($discountCode->id)],
            'percentage'       => 'required|numeric|min:0.01|max:100',
            'beneficiary_name' => 'required|string|max:255',
            'status'           => ['required', Rule::in(DiscountCode::STATUSES)],
        ]);

        $validated['code'] = Str::upper(trim($validated['code']));

        $discountCode->update($validated);

        return redirect()->route('discount-codes.index')->with('success', 'Código de descuento actualizado correctamente.');
    }

    public function destroy(DiscountCode $discountCode): RedirectResponse
    {
        $discountCode->delete();
        return redirect()->route('discount-codes.index')->with('success', 'Código de descuento eliminado correctamente.');
    }
}
