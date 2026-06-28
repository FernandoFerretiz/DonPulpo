<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View|RedirectResponse
    {
        if (Auth::user()->role === 'waiter') {
            return redirect()->route('pos');
        }

        return view('dashboard.index');
    }
}
