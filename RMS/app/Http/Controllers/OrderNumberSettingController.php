<?php

namespace App\Http\Controllers;

use App\Models\PosOrderNumberSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OrderNumberSettingController extends Controller
{
    public function edit(): View
    {
        $setting = PosOrderNumberSetting::current();
        return view('order-number-settings.edit', compact('setting'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'mode'        => ['required', Rule::in(['sequential', 'random'])],
            'next_number' => 'required|integer|min:1|max:999999',
        ]);

        $setting = PosOrderNumberSetting::current();
        $setting->update($validated);

        return redirect()->route('order-number-settings.edit')
            ->with('status', 'Configuración de numeración de órdenes actualizada.');
    }
}
