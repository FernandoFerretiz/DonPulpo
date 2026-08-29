@extends('layouts.app')
@section('title', 'Modificadores — Don Pulpo RMS')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 mt-2">
    <h2 class="h4 mb-0">Modificadores</h2>
    <a href="{{ route('modifier-groups.create') }}" class="btn btn-dp">+ Nuevo grupo</a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Nombre</th>
                    <th>Tipo</th>
                    <th>Obligatorio</th>
                    <th>Opciones</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($groups as $group)
                <tr>
                    <td class="fw-semibold">{{ $group->name }}</td>
                    <td>{{ $group->selection_type === 'single' ? 'Selección única' : 'Selección múltiple' }}</td>
                    <td>{{ $group->required ? 'Sí' : 'No' }}</td>
                    <td>
                        @foreach($group->options as $option)
                            <span class="badge bg-secondary me-1">{{ $option->name }}</span>
                        @endforeach
                        <div class="form-text mb-0">El precio se define por platillo.</div>
                    </td>
                    <td>
                        <a href="{{ route('modifier-groups.edit', $group) }}" class="btn btn-sm btn-secondary">Editar</a>
                        <form action="{{ route('modifier-groups.destroy', $group) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('¿Eliminar este grupo de modificadores? Se quitará de todos los platillos que lo usen.');">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-4">Sin grupos de modificadores todavía.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
