@extends('layouts.app')
@section('title', 'Kardex — ' . $product->name . ' — Don Pulpo RMS')
@section('content')
<div class="d-flex align-items-center gap-2 mb-3 mt-2">
    <a href="{{ route('inventory.products.index') }}" class="btn btn-sm btn-outline-secondary">← Volver</a>
    <h2 class="h4 mb-0">Kardex: {{ $product->name }} <small class="text-muted">({{ $product->internal_code }})</small></h2>
</div>

<div class="row mb-3">
    <div class="col-md-3 mb-2">
        <div class="card shadow-sm border-0 h-100"><div class="card-body">
            <div class="text-muted small">Existencia total</div>
            <div class="h5 mb-0">{{ rtrim(rtrim(number_format($product->totalStock(), 3), '0'), '.') }} {{ $product->unitOfMeasure->abbreviation }}</div>
        </div></div>
    </div>
    <div class="col-md-3 mb-2">
        <div class="card shadow-sm border-0 h-100"><div class="card-body">
            <div class="text-muted small">Costo promedio</div>
            <div class="h5 mb-0">${{ number_format($product->average_cost, 4) }}</div>
        </div></div>
    </div>
    <div class="col-md-3 mb-2">
        <div class="card shadow-sm border-0 h-100"><div class="card-body">
            <div class="text-muted small">Último costo</div>
            <div class="h5 mb-0">${{ number_format($product->last_cost, 4) }}</div>
        </div></div>
    </div>
    <div class="col-md-3 mb-2">
        <div class="card shadow-sm border-0 h-100"><div class="card-body">
            <div class="text-muted small">Mín. / Máx.</div>
            <div class="h5 mb-0">{{ (float) $product->min_stock }} / {{ (float) $product->max_stock }}</div>
        </div></div>
    </div>
</div>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-transparent fw-semibold">Existencia por almacén</div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light"><tr><th>Almacén</th><th>Cantidad</th></tr></thead>
            <tbody>
                @forelse($stocks as $stock)
                    <tr><td>{{ $stock->warehouse->name }}</td><td>{{ rtrim(rtrim(number_format($stock->quantity, 3), '0'), '.') }}</td></tr>
                @empty
                    <tr><td colspan="2" class="text-center text-muted py-3">Sin existencias registradas todavía.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header bg-transparent fw-semibold">Movimientos (Kardex)</div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Fecha</th>
                    <th>Folio</th>
                    <th>Tipo</th>
                    <th>Almacén</th>
                    <th>Cantidad</th>
                    <th>Saldo anterior</th>
                    <th>Saldo nuevo</th>
                    <th>Costo unit.</th>
                    <th>Usuario</th>
                    <th>Notas</th>
                </tr>
            </thead>
            <tbody>
                @forelse($movements as $mov)
                <tr>
                    <td>{{ $mov->movement_date->format('d/m/Y H:i') }}</td>
                    <td><code>{{ $mov->folio }}</code></td>
                    <td>{{ $mov->getTypeLabel() }}</td>
                    <td>
                        {{ $mov->warehouse->name }}
                        @if($mov->relatedWarehouse)
                            <span class="text-muted">→ {{ $mov->relatedWarehouse->name }}</span>
                        @endif
                    </td>
                    <td class="{{ $mov->quantity >= 0 ? 'text-success' : 'text-danger' }}">
                        {{ $mov->quantity >= 0 ? '+' : '' }}{{ rtrim(rtrim(number_format($mov->quantity, 3), '0'), '.') }}
                    </td>
                    <td>{{ rtrim(rtrim(number_format($mov->previous_balance, 3), '0'), '.') }}</td>
                    <td>{{ rtrim(rtrim(number_format($mov->new_balance, 3), '0'), '.') }}</td>
                    <td>${{ number_format($mov->unit_cost, 4) }}</td>
                    <td>{{ $mov->user?->name ?? '—' }}</td>
                    <td>{{ $mov->notes }}</td>
                </tr>
                @empty
                <tr><td colspan="10" class="text-center text-muted py-4">Este producto todavía no tiene movimientos.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $movements->links() }}</div>
@endsection
