@extends('layouts.app')
@section('title', 'Clientes — Don Pulpo RMS')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 mt-2">
    <h2 class="h4 mb-0">Clientes</h2>
    <a href="{{ route('customers.create') }}" class="btn btn-dp">+ Nuevo cliente</a>
</div>

<form method="GET" action="{{ route('customers.index') }}" class="mb-3">
    <div class="input-group" style="max-width:380px;">
        <input type="search" name="search" class="form-control" placeholder="Buscar por nombre o teléfono…"
               value="{{ $search ?? '' }}" autocomplete="off">
        <button class="btn btn-outline-secondary" type="submit">Buscar</button>
        @if($search)
            <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">✕</a>
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
                    <th>Teléfono</th>
                    <th>Saldo adeudado</th>
                    <th>Límite de crédito</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $customer)
                <tr>
                    <td>{{ $customer->id }}</td>
                    <td>{{ $customer->name }}</td>
                    <td>{{ $customer->phone ?? '—' }}</td>
                    <td>
                        <span class="{{ $customer->balance > 0 ? 'text-danger fw-semibold' : '' }}">
                            ${{ number_format($customer->balance, 2) }}
                        </span>
                    </td>
                    <td>{{ $customer->credit_limit !== null ? '$'.number_format($customer->credit_limit, 2) : 'Sin límite' }}</td>
                    <td class="d-flex gap-1">
                        <a href="{{ route('customers.edit', $customer) }}" class="btn btn-sm btn-outline-secondary">Editar</a>

                        @if($customer->balance > 0)
                        <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#payModal{{ $customer->id }}">
                            Registrar abono
                        </button>
                        @endif

                        <form action="{{ route('customers.destroy', $customer) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('¿Eliminar este cliente?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Eliminar</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-4">No hay clientes registrados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $customers->links() }}</div>

@foreach($customers as $customer)
    @if($customer->balance > 0)
    <div class="modal fade" id="payModal{{ $customer->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('customers.payment', $customer) }}" method="POST">
                    @csrf @method('PATCH')
                    <div class="modal-header">
                        <h5 class="modal-title">Registrar abono — {{ $customer->name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted">Saldo actual: <strong>${{ number_format($customer->balance, 2) }}</strong></p>
                        <label class="form-label fw-semibold">Monto del abono</label>
                        <input type="number" name="amount" class="form-control" min="0.01"
                               max="{{ $customer->balance }}" step="0.01" required />
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-dp">Registrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
@endforeach
@endsection
