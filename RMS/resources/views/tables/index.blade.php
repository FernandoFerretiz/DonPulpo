@extends('layouts.app')
@section('title', 'Mesas — Don Pulpo RMS')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 mt-2">
    <h2 class="h4 mb-0">Mesas</h2>
    <a href="{{ route('tables.create') }}" class="btn btn-dp">+ Nueva mesa</a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Capacidad (personas)</th>
                    <th>Orden</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tables as $t)
                <tr>
                    <td>{{ $t->id }}</td>
                    <td>{{ $t->name }}</td>
                    <td>{{ $t->capacity }}</td>
                    <td>{{ $t->display_order }}</td>
                    <td>
                        <span class="badge {{ $t->active ? 'badge-active' : 'badge-inactive' }}">
                            {{ $t->active ? 'Visible en app' : 'Oculta' }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('tables.edit', $t) }}" class="btn btn-sm btn-outline-secondary">Editar</a>
                        <form action="{{ route('tables.destroy', $t) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('¿Eliminar esta mesa?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Eliminar</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-4">No hay mesas registradas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
