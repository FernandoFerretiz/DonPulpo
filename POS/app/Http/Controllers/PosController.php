<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PosController extends Controller
{
    public function index(): View
    {
        if (Auth::user()->role === 'waiter') {
            return view('pos.vista-mesero');
        }

        return view('pos.vista-pos');
    }
}
