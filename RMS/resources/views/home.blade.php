@extends('layouts.app')
@section('title', 'Dashboard — Don Pulpo RMS')
@section('content')
<div class="row mt-2 mb-4">
    <div class="col">
        <h1 class="h3">🐙 Don Pulpo — Panel de Administración</h1>
        <p class="text-muted">Restaurant Management System</p>
    </div>
</div>
<div class="row g-4">
    <div class="col-md-4">
        <div class="card shadow-sm border-0">
            <div class="card-body text-center py-4">
                <div class="display-4 mb-2">👥</div>
                <h2 class="h1 fw-bold">{{ $totalUsers }}</h2>
                <p class="text-muted mb-3">Usuarios registrados</p>
                <a href="{{ route('users.index') }}" class="btn btn-dp w-100">Administrar usuarios</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border-0">
            <div class="card-body text-center py-4">
                <div class="display-4 mb-2">📂</div>
                <h2 class="h1 fw-bold">{{ $totalCategories }}</h2>
                <p class="text-muted mb-3">Categorías del menú</p>
                <a href="{{ route('dish-categories.index') }}" class="btn btn-dp w-100">Administrar categorías</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border-0">
            <div class="card-body text-center py-4">
                <div class="display-4 mb-2">🍽️</div>
                <h2 class="h1 fw-bold">{{ $totalDishes }}</h2>
                <p class="text-muted mb-3">Platillos en el menú</p>
                <a href="{{ route('dishes.index') }}" class="btn btn-dp w-100">Administrar platillos</a>
            </div>
        </div>
    </div>
</div>
@endsection
