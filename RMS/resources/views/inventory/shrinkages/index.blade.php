@extends('layouts.app')
@section('title', 'Mermas — Don Pulpo RMS')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 mt-2">
    <h2 class="h4 mb-0">Mermas</h2>
    <a href="{{ route('inventory.shrinkages.create') }}" class="btn btn-dp">+ Nueva merma</a>
</div>

<ul class="nav nav-pills mb-3">
    @foreach($statusTabs as $value => $label)
        <li class="nav-item">
            <a class="nav-link {{ (string) $status === (string) $value ? 'active' : '' }}"
               href="{{ route('inventory.shrinkages.index', $value ? ['status' => $value] : []) }}">{{ $label }}</a>
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
                @forelse($shrinkages as $s)
                <tr>
                    <td><code>{{ $s->folio }}</code></td>
                    <td>{{ $s->warehouse->name }}</td>
                    <td>{{ $s->getReasonLabel() }}</td>
                    <td>{{ $s->shrinkage_date->format('d/m/Y') }}</td>
                    <td>
                        <span class="badge {{ $s->status === 'completed' ? 'badge-active' : ($s->status === 'cancelled' ? 'badge-inactive' : 'bg-secondary') }}">
                            {{ $s->getStatusLabel() }}
                        </span>
                    </td>
                    <td><a href="{{ route('inventory.shrinkages.show', $s) }}" class="btn btn-sm btn-outline-secondary">Ver</a></td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-4">No hay mermas registradas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $shrinkages->links() }}</div>
@endsection
