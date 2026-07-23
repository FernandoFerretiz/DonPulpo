@extends('layouts.app')
@section('title', 'Transferencias — Don Pulpo RMS')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 mt-2">
    <h2 class="h4 mb-0">Transferencias entre almacenes</h2>
    <a href="{{ route('inventory.transfers.create') }}" class="btn btn-dp">+ Nueva transferencia</a>
</div>

<ul class="nav nav-pills mb-3">
    @foreach($statusTabs as $value => $label)
        <li class="nav-item">
            <a class="nav-link {{ (string) $status === (string) $value ? 'active' : '' }}"
               href="{{ route('inventory.transfers.index', $value ? ['status' => $value] : []) }}">{{ $label }}</a>
        </li>
    @endforeach
</ul>

<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Folio</th>
                    <th>Origen</th>
                    <th>Destino</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transfers as $t)
                <tr>
                    <td><code>{{ $t->folio }}</code></td>
                    <td>{{ $t->originWarehouse->name }}</td>
                    <td>{{ $t->destinationWarehouse->name }}</td>
                    <td>{{ $t->transfer_date->format('d/m/Y') }}</td>
                    <td>
                        <span class="badge {{ $t->status === 'completed' ? 'badge-active' : ($t->status === 'cancelled' ? 'badge-inactive' : 'bg-secondary') }}">
                            {{ $t->getStatusLabel() }}
                        </span>
                    </td>
                    <td><a href="{{ route('inventory.transfers.show', $t) }}" class="btn btn-sm btn-outline-secondary">Ver</a></td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-4">No hay transferencias registradas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $transfers->links() }}</div>
@endsection
