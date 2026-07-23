@extends('layouts.app')
@section('title', 'Ajuste ' . $adjustment->folio . ' — Don Pulpo RMS')
@section('content')
<div class="d-flex align-items-center gap-2 mb-3 mt-2">
    <a href="{{ route('inventory.adjustments.index') }}" class="btn btn-sm btn-outline-secondary">← Volver</a>
    <h2 class="h4 mb-0">Ajuste {{ $adjustment->folio }}</h2>
    <span class="badge {{ $adjustment->status === 'completed' ? 'badge-active' : ($adjustment->status === 'cancelled' ? 'badge-inactive' : 'bg-secondary') }}">
        {{ $adjustment->getStatusLabel() }}
    </span>
</div>

<div class="row mb-3">
    <div class="col-md-4"><strong>Almacén:</strong> {{ $adjustment->warehouse->name }}</div>
    <div class="col-md-4"><strong>Motivo:</strong> {{ $adjustment->reason }}</div>
    <div class="col-md-4"><strong>Fecha:</strong> {{ $adjustment->adjustment_date->format('d/m/Y') }}</div>
</div>
@if($adjustment->notes)
    <div class="mb-3"><strong>Notas:</strong> {{ $adjustment->notes }}</div>
@endif

<div class="card shadow-sm border-0 mb-3">
    <div class="table-responsive">
        <table class="table mb-0">
            <thead class="table-light"><tr><th>Producto</th><th>Anterior</th><th>Nueva</th><th>Diferencia</th></tr></thead>
            <tbody>
                @foreach($adjustment->items as $item)
                <tr>
                    <td>{{ $item->product->name }}</td>
                    <td>{{ rtrim(rtrim(number_format($item->previous_quantity, 3), '0'), '.') }}</td>
                    <td>{{ rtrim(rtrim(number_format($item->new_quantity, 3), '0'), '.') }}</td>
                    <td class="{{ $item->difference >= 0 ? 'text-success' : 'text-danger' }}">
                        {{ $item->difference >= 0 ? '+' : '' }}{{ rtrim(rtrim(number_format($item->difference, 3), '0'), '.') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@if($adjustment->isDraft())
<div class="d-flex gap-2">
    <form action="{{ route('inventory.adjustments.complete', $adjustment) }}" method="POST" onsubmit="return confirm('¿Aplicar este ajuste al inventario?')">
        @csrf @method('PATCH')
        <button class="btn btn-dp">Aplicar ajuste</button>
    </form>
    <form action="{{ route('inventory.adjustments.cancel', $adjustment) }}" method="POST" onsubmit="return confirm('¿Cancelar este ajuste?')">
        @csrf @method('PATCH')
        <button class="btn btn-outline-danger">Cancelar</button>
    </form>
</div>
@endif
@endsection
