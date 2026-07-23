@extends('layouts.app')
@section('title', 'Almacenes — Don Pulpo RMS')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 mt-2">
    <h2 class="h4 mb-0">Almacenes</h2>
    <a href="{{ route('inventory.warehouses.create') }}" class="btn btn-dp">+ Nuevo almacén</a>
</div>

<form method="GET" action="{{ route('inventory.warehouses.index') }}" class="mb-3">
    <div class="input-group" style="max-width:380px;">
        <input type="search" name="search" class="form-control" placeholder="Buscar almacén…"
               value="{{ $search ?? '' }}" autocomplete="off">
        <button class="btn btn-outline-secondary" type="submit">Buscar</button>
        @if($search)
            <a href="{{ route('inventory.warehouses.index') }}" class="btn btn-outline-secondary">✕</a>
        @endif
    </div>
</form>

<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Slug</th>
                    <th>Productos con existencia</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($warehouses as $wh)
                <tr>
                    <td>{{ $wh->id }}</td>
                    <td>{{ $wh->name }}</td>
                    <td><code>{{ $wh->slug }}</code></td>
                    <td>{{ $wh->stocks_count }}</td>
                    <td>
                        <span class="badge {{ $wh->status === 'active' ? 'badge-active' : 'badge-inactive' }}">
                            {{ $wh->getStatusLabel() }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('inventory.warehouses.edit', $wh) }}" class="btn btn-sm btn-outline-secondary">Editar</a>
                        <form action="{{ route('inventory.warehouses.destroy', $wh) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('¿Eliminar este almacén?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Eliminar</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-4">No hay almacenes registrados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $warehouses->links() }}</div>
@endsection
