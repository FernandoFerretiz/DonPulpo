@extends('layouts.app')
@section('title', 'Ajustes de inventario — Don Pulpo RMS')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 mt-2">
    <h2 class="h4 mb-0">Ajustes de inventario</h2>
    <a href="{{ route('inventory.adjustments.create') }}" class="btn btn-dp">+ Nuevo ajuste</a>
</div>

<ul class="nav nav-pills mb-3">
    @foreach($statusTabs as $value => $label)
        <li class="nav-item">
            <a class="nav-link {{ (string) $status === (string) $value ? 'active' : '' }}"
               href="{{ route('inventory.adjustments.index', $value ? ['status' => $value] : []) }}">{{ $label }}</a>
        </li>
    @endforeach
</ul>

<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr><th>Folio</th><th>Almacén</th><th>Motivo</th><th>Fecha</th><th>Estado</th><th>Acciones</th></tr>
            </thead>
            <tbody>
                @forelse($adjustments as $a)
                <tr>
                    <td><code>{{ $a->folio }}</code></td>
                    <td>{{ $a->warehouse->name }}</td>
                    <td>{{ $a->reason }}</td>
                    <td>{{ $a->adjustment_date->format('d/m/Y') }}</td>
                    <td>
                        <span class="badge {{ $a->status === 'completed' ? 'badge-active' : ($a->status === 'cancelled' ? 'badge-inactive' : 'bg-secondary') }}">
                            {{ $a->getStatusLabel() }}
                        </span>
                    </td>
                    <td><a href="{{ route('inventory.adjustments.show', $a) }}" class="btn btn-sm btn-outline-secondary">Ver</a></td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-4">No hay ajustes registrados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $adjustments->links() }}</div>
@endsection
