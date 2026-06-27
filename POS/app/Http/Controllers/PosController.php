<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class PosController extends Controller
{
    public function index(): View
    {
        return view('pos.vista-pos');
    }
}
