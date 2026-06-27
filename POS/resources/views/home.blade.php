@extends('layouts.app')
@section('title', 'Inicio — Don Pulpo POS')
@section('content')
<div class="row justify-content-center mt-4">
    <div class="col-md-8">
        <div class="text-center mb-5">
            <div style="font-size:60px">🐙</div>
            <h1 class="h2 fw-bold mt-2" style="color:#04152c">Don Pulpo POS</h1>
            <p class="text-muted">Bienvenido, <strong>{{ Auth::user()->name }}</strong></p>
        </div>

        <div class="row g-4">
            <div class="col-md-6">
                <a href="{{ route('pos') }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 text-center p-5"
                         style="border-radius:24px; background: linear-gradient(135deg,#04152c,#046577 52%,#09d1d0); color:#fff; transition:.15s">
                        <div style="font-size:64px">🛒</div>
                        <h2 class="h4 fw-bold mt-3">Ir al POS</h2>
                        <p class="mb-0 opacity-75">Tomar órdenes y cobrar</p>
                    </div>
                </a>
            </div>
            <div class="col-md-6">
                <a href="{{ route('dashboard') }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 text-center p-5"
                         style="border-radius:24px; background: linear-gradient(135deg,#04152c,#087ccb 52%,#09d1d0); color:#fff; transition:.15s">
                        <div style="font-size:64px">📊</div>
                        <h2 class="h4 fw-bold mt-3">Dashboard</h2>
                        <p class="mb-0 opacity-75">Ventas y resumen del día</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
