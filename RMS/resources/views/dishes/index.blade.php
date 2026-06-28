@extends('layouts.app')
@section('title', 'Platillos — Don Pulpo RMS')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 mt-2">
    <h2 class="h4 mb-0">Platillos</h2>
    <a href="{{ route('dishes.create') }}" class="btn btn-dp">+ Nuevo platillo</a>
</div>

<form method="GET" action="{{ route('dishes.index') }}" class="mb-3">
    <div class="input-group" style="max-width:380px;">
        <input type="search" name="search" class="form-control" placeholder="Buscar platillo…"
               value="{{ $search ?? '' }}" autocomplete="off">
        <button class="btn btn-outline-secondary" type="submit">Buscar</button>
        @if($search)
            <a href="{{ route('dishes.index') }}" class="btn btn-outline-secondary">✕</a>
        @endif
    </div>
</form>

<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width:64px"></th>
                    <th>Nombre</th>
                    <th>Categoría</th>
                    <th>Precio</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($dishes as $dish)
                <tr>
                    <td>
                        @if($dish->image_path)
                            <img src="{{ asset('storage/' . $dish->image_path) }}" alt="{{ $dish->name }}"
                                 style="width:52px;height:52px;object-fit:cover;border-radius:10px;border:1px solid #dee2e6;" />
                        @else
                            <div style="width:52px;height:52px;border-radius:10px;background:#f2f6f9;display:grid;place-items:center;font-size:22px;color:#adb5bd;">
                                🍽️
                            </div>
                        @endif
                    </td>
                    <td>
                        <div class="fw-semibold">{{ $dish->name }}</div>
                        @if($dish->description)
                            <small class="text-muted">{{ Str::limit($dish->description, 70) }}</small>
                        @endif
                    </td>
                    <td>{{ $dish->category?->name ?? '—' }}</td>
                    <td>${{ number_format($dish->price, 2) }}</td>
                    <td>
                        @php
                            $badgeClass = match($dish->status) {
                                'active' => 'badge-active',
                                'temporarily_inactive' => 'badge-tmp',
                                default => 'badge-inactive',
                            };
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ $dish->getStatusLabel() }}</span>
                    </td>
                    <td>
                        <a href="{{ route('dishes.edit', $dish) }}" class="btn btn-sm btn-outline-secondary">Editar</a>
                        <form action="{{ route('dishes.destroy', $dish) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('¿Eliminar este platillo?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Eliminar</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-4">No hay platillos registrados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $dishes->links() }}</div>
@endsection
