@extends('layouts.app')
@section('title', 'Proveedores — Don Pulpo RMS')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 mt-2">
    <h2 class="h4 mb-0">Proveedores</h2>
    <a href="{{ route('inventory.suppliers.create') }}" class="btn btn-dp">+ Nuevo proveedor</a>
</div>

<form method="GET" action="{{ route('inventory.suppliers.index') }}" class="mb-3">
    <div class="input-group" style="max-width:380px;">
        <input type="search" name="search" class="form-control" placeholder="Buscar proveedor…"
               value="{{ $search ?? '' }}" autocomplete="off">
        <button class="btn btn-outline-secondary" type="submit">Buscar</button>
        @if($search)
            <a href="{{ route('inventory.suppliers.index') }}" class="btn btn-outline-secondary">✕</a>
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
                    <th>Contacto</th>
                    <th>Teléfono</th>
                    <th>Email</th>
                    <th>Productos</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($suppliers as $sup)
                <tr>
                    <td>{{ $sup->id }}</td>
                    <td>{{ $sup->name }}</td>
                    <td>{{ $sup->contact_name }}</td>
                    <td>{{ $sup->phone }}</td>
                    <td>{{ $sup->email }}</td>
                    <td>{{ $sup->products_count }}</td>
                    <td>
                        <span class="badge {{ $sup->status === 'active' ? 'badge-active' : 'badge-inactive' }}">
                            {{ $sup->getStatusLabel() }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('inventory.suppliers.edit', $sup) }}" class="btn btn-sm btn-outline-secondary">Editar</a>
                        <form action="{{ route('inventory.suppliers.destroy', $sup) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('¿Eliminar este proveedor?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Eliminar</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted py-4">No hay proveedores registrados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $suppliers->links() }}</div>
@endsection
