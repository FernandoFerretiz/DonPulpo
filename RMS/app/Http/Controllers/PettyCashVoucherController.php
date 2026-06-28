<?php

namespace App\Http\Controllers;

use App\Models\PettyCashCategory;
use App\Models\PettyCashVoucher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PettyCashVoucherController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status');

        $query = PettyCashVoucher::with(['requestedBy', 'authorizedBy', 'rejectedBy', 'category'])
            ->orderByDesc('created_at');

        if ($status) {
            $query->where('status', $status);
        }

        $vouchers   = $query->paginate(20)->withQueryString();
        $statusTabs = [
            ''           => 'Todos',
            'pending'    => 'Pendientes',
            'authorized' => 'Autorizados',
            'paid'       => 'Pagados',
            'rejected'   => 'Rechazados',
            'cancelled'  => 'Cancelados',
        ];

        return view('petty-cash.vouchers.index', compact('vouchers', 'status', 'statusTabs'));
    }

    public function create(): View
    {
        $categories = PettyCashCategory::active()->orderBy('name')->get();
        return view('petty-cash.vouchers.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'petty_cash_category_id' => 'nullable|exists:petty_cash_categories,id',
            'beneficiary'            => 'nullable|string|max:255',
            'concept'                => 'required|string|max:1000',
            'amount'                 => 'required|numeric|min:0.01',
            'notes'                  => 'nullable|string|max:1000',
        ]);

        PettyCashVoucher::create(array_merge($validated, [
            'folio'        => PettyCashVoucher::generateFolio(),
            'requested_by' => auth()->id(),
            'status'       => PettyCashVoucher::STATUS_PENDING,
            'requested_at' => now(),
        ]));

        return redirect()->route('petty-cash.vouchers.index')
            ->with('success', 'Vale creado correctamente.');
    }

    public function authorize(PettyCashVoucher $voucher): RedirectResponse
    {
        if (!$voucher->isPending()) {
            return back()->with('error', 'Solo se pueden autorizar vales pendientes.');
        }

        $voucher->update([
            'status'        => PettyCashVoucher::STATUS_AUTHORIZED,
            'authorized_by' => auth()->id(),
            'authorized_at' => now(),
        ]);

        return back()->with('success', "Vale {$voucher->folio} autorizado.");
    }

    public function reject(Request $request, PettyCashVoucher $voucher): RedirectResponse
    {
        if (!$voucher->isPending()) {
            return back()->with('error', 'Solo se pueden rechazar vales pendientes.');
        }

        $request->validate(['rejection_reason' => 'required|string|max:500']);

        $voucher->update([
            'status'           => PettyCashVoucher::STATUS_REJECTED,
            'rejected_by'      => auth()->id(),
            'rejected_at'      => now(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        return back()->with('success', "Vale {$voucher->folio} rechazado.");
    }

    public function cancel(PettyCashVoucher $voucher): RedirectResponse
    {
        if ($voucher->isPaid()) {
            return back()->with('error', 'No se puede cancelar un vale ya pagado.');
        }

        $voucher->update([
            'status'       => PettyCashVoucher::STATUS_CANCELLED,
            'cancelled_at' => now(),
        ]);

        return back()->with('success', "Vale {$voucher->folio} cancelado.");
    }
}
