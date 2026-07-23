@extends('layouts.app')
@section('title', 'Conteos físicos — Don Pulpo RMS')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 mt-2">
    <h2 class="h4 mb-0">Conteos físicos</h2>
    <a href="{{ route('inventory.physical-counts.create') }}" class="btn btn-dp">+ Nuevo conteo</a>
</div>

<ul class="nav nav-pills mb-3">
    @foreach($statusTabs as $value => $label)
        <li class="nav-item">
            <a class="nav-link {{ (string) $status === (string) $value ? 'active' : '' }}"
               href="{{ route('inventory.physical-counts.index', $value ? ['status' => $value] : []) }}">{{ $label }}</a>
        </li>
    @endforeach
</ul>

<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr><th>Folio</th><th>Almacén</th><th>Fecha</th><th>Estado</th><th>Acciones</th></tr>
            </thead>
            <tbody>
                @forelse($counts as $c)
                <tr>
                    <td><code>{{ $c->folio }}</code></td>
                    <td>{{ $c->warehouse->name }}</td>
                    <td>{{ $c->count_date->format('d/m/Y') }}</td>
                    <td>
                        <span class="badge {{ $c->status === 'confirmed' ? 'badge-active' : ($c->status === 'cancelled' ? 'badge-inactive' : 'bg-secondary') }}">
                            {{ $c->getStatusLabel() }}
                        </span>
                    </td>
                    <td><a href="{{ route('inventory.physical-counts.show', $c) }}" class="btn btn-sm btn-outline-secondary">Ver</a></td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-4">No hay conteos registrados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $counts->links() }}</div>
@endsection
