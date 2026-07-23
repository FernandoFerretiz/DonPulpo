@extends('layouts.app')
@section('title', 'Unidades de medida — Don Pulpo RMS')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 mt-2">
    <h2 class="h4 mb-0">Unidades de medida</h2>
    <a href="{{ route('inventory.units.create') }}" class="btn btn-dp">+ Nueva unidad</a>
</div>

<form method="GET" action="{{ route('inventory.units.index') }}" class="mb-3">
    <div class="input-group" style="max-width:380px;">
        <input type="search" name="search" class="form-control" placeholder="Buscar unidad…"
               value="{{ $search ?? '' }}" autocomplete="off">
        <button class="btn btn-outline-secondary" type="submit">Buscar</button>
        @if($search)
            <a href="{{ route('inventory.units.index') }}" class="btn btn-outline-secondary">✕</a>
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
                    <th>Abreviatura</th>
                    <th>Unidad base</th>
                    <th>Factor de conversión</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($units as $unit)
                <tr>
                    <td>{{ $unit->id }}</td>
                    <td>{{ $unit->name }}</td>
                    <td><code>{{ $unit->abbreviation }}</code></td>
                    <td>{{ $unit->baseUnit?->name ?? '— (es unidad base)' }}</td>
                    <td>{{ rtrim(rtrim(number_format($unit->conversion_factor, 4), '0'), '.') }}</td>
                    <td>
                        <a href="{{ route('inventory.units.edit', $unit) }}" class="btn btn-sm btn-outline-secondary">Editar</a>
                        <form action="{{ route('inventory.units.destroy', $unit) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('¿Eliminar esta unidad?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Eliminar</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-4">No hay unidades registradas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $units->links() }}</div>
@endsection
