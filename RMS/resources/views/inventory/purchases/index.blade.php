@extends('layouts.app')
@section('title', 'Compras — Don Pulpo RMS')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 mt-2">
    <h2 class="h4 mb-0">Compras</h2>
    <a href="{{ route('inventory.purchases.create') }}" class="btn btn-dp">+ Nueva compra</a>
</div>

<ul class="nav nav-pills mb-3">
    @foreach($statusTabs as $value => $label)
        <li class="nav-item">
            <a class="nav-link {{ (string) $status === (string) $value ? 'active' : '' }}"
               href="{{ route('inventory.purchases.index', $value ? ['status' => $value] : []) }}">{{ $label }}</a>
        </li>
    @endforeach
</ul>

<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Folio</th>
                    <th>Proveedor</th>
                    <th>Almacén</th>
                    <th>Factura</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($purchases as $p)
                <tr>
                    <td><code>{{ $p->folio }}</code></td>
                    <td>{{ $p->supplier->name }}</td>
                    <td>{{ $p->warehouse->name }}</td>
                    <td>{{ $p->invoice_number ?? '—' }}</td>
                    <td>{{ $p->purchase_date->format('d/m/Y') }}</td>
                    <td>
                        <span class="badge {{ $p->status === 'received' ? 'badge-active' : ($p->status === 'cancelled' ? 'badge-inactive' : 'bg-secondary') }}">
                            {{ $p->getStatusLabel() }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('inventory.purchases.show', $p) }}" class="btn btn-sm btn-outline-secondary">Ver</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-4">No hay compras registradas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $purchases->links() }}</div>
@endsection
