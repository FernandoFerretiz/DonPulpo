@extends('layouts.app')
@section('title', 'Categorías de inventario — Don Pulpo RMS')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 mt-2">
    <h2 class="h4 mb-0">Categorías de inventario</h2>
    <a href="{{ route('inventory.categories.create') }}" class="btn btn-dp">+ Nueva categoría</a>
</div>

<form method="GET" action="{{ route('inventory.categories.index') }}" class="mb-3">
    <div class="input-group" style="max-width:380px;">
        <input type="search" name="search" class="form-control" placeholder="Buscar categoría…"
               value="{{ $search ?? '' }}" autocomplete="off">
        <button class="btn btn-outline-secondary" type="submit">Buscar</button>
        @if($search)
            <a href="{{ route('inventory.categories.index') }}" class="btn btn-outline-secondary">✕</a>
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
                    <th>Productos</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $cat)
                <tr>
                    <td>{{ $cat->id }}</td>
                    <td>{{ $cat->name }}</td>
                    <td><code>{{ $cat->slug }}</code></td>
                    <td>{{ $cat->products_count }}</td>
                    <td>
                        <span class="badge {{ $cat->status === 'active' ? 'badge-active' : 'badge-inactive' }}">
                            {{ $cat->getStatusLabel() }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('inventory.categories.edit', $cat) }}" class="btn btn-sm btn-outline-secondary">Editar</a>
                        <form action="{{ route('inventory.categories.destroy', $cat) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('¿Eliminar esta categoría?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Eliminar</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-4">No hay categorías registradas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $categories->links() }}</div>
@endsection
