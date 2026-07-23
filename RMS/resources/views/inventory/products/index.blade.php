@extends('layouts.app')
@section('title', 'Productos de inventario — Don Pulpo RMS')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 mt-2">
    <h2 class="h4 mb-0">Productos de inventario</h2>
    <a href="{{ route('inventory.products.create') }}" class="btn btn-dp">+ Nuevo producto</a>
</div>

<form method="GET" action="{{ route('inventory.products.index') }}" class="mb-3">
    <div class="input-group" style="max-width:380px;">
        <input type="search" name="search" class="form-control" placeholder="Buscar por nombre o código…"
               value="{{ $search ?? '' }}" autocomplete="off">
        <button class="btn btn-outline-secondary" type="submit">Buscar</button>
        @if($search)
            <a href="{{ route('inventory.products.index') }}" class="btn btn-outline-secondary">✕</a>
        @endif
    </div>
</form>

<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Categoría</th>
                    <th>Unidad</th>
                    <th>Costo prom.</th>
                    <th>Existencia total</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $p)
                <tr>
                    <td>{{ $p->id }}</td>
                    <td><code>{{ $p->internal_code }}</code></td>
                    <td>{{ $p->name }}</td>
                    <td>{{ $p->category?->name ?? '—' }}</td>
                    <td>{{ $p->unitOfMeasure->abbreviation }}</td>
                    <td>${{ number_format($p->average_cost, 2) }}</td>
                    <td>
                        {{ rtrim(rtrim(number_format($p->totalStock(), 3), '0'), '.') }}
                        @if($p->isBelowMinimum())
                            <span class="badge bg-warning text-dark ms-1">bajo mínimo</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge {{ $p->is_active ? 'badge-active' : 'badge-inactive' }}">
                            {{ $p->is_active ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('inventory.products.show', $p) }}" class="btn btn-sm btn-outline-secondary">Kardex</a>
                        <a href="{{ route('inventory.products.edit', $p) }}" class="btn btn-sm btn-outline-secondary">Editar</a>
                        <form action="{{ route('inventory.products.destroy', $p) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('¿Eliminar este producto?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Eliminar</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" class="text-center text-muted py-4">No hay productos de inventario registrados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $products->links() }}</div>
@endsection
