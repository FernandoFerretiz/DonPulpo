@extends('layouts.app')
@section('title', 'Merma ' . $shrinkage->folio . ' — Don Pulpo RMS')
@section('content')
<div class="d-flex align-items-center gap-2 mb-3 mt-2">
    <a href="{{ route('inventory.shrinkages.index') }}" class="btn btn-sm btn-outline-secondary">← Volver</a>
    <h2 class="h4 mb-0">Merma {{ $shrinkage->folio }}</h2>
    <span class="badge {{ $shrinkage->status === 'completed' ? 'badge-active' : ($shrinkage->status === 'cancelled' ? 'badge-inactive' : 'bg-secondary') }}">
        {{ $shrinkage->getStatusLabel() }}
    </span>
</div>

<div class="row mb-3">
    <div class="col-md-4"><strong>Almacén:</strong> {{ $shrinkage->warehouse->name }}</div>
    <div class="col-md-4"><strong>Motivo:</strong> {{ $shrinkage->getReasonLabel() }}</div>
    <div class="col-md-4"><strong>Fecha:</strong> {{ $shrinkage->shrinkage_date->format('d/m/Y') }}</div>
</div>
@if($shrinkage->notes)
    <div class="mb-3"><strong>Notas:</strong> {{ $shrinkage->notes }}</div>
@endif

<div class="card shadow-sm border-0 mb-3">
    <div class="table-responsive">
        <table class="table mb-0">
            <thead class="table-light"><tr><th>Producto</th><th>Cantidad</th><th>Costo unit.</th><th>Valor perdido</th></tr></thead>
            <tbody>
                @foreach($shrinkage->items as $item)
                <tr>
                    <td>{{ $item->product->name }}</td>
                    <td>{{ rtrim(rtrim(number_format($item->quantity, 3), '0'), '.') }} {{ $item->product->unitOfMeasure->abbreviation }}</td>
                    <td>${{ number_format($item->unit_cost, 4) }}</td>
                    <td>${{ number_format($item->quantity * $item->unit_cost, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@if($shrinkage->isDraft())
<div class="d-flex gap-2">
    <form action="{{ route('inventory.shrinkages.complete', $shrinkage) }}" method="POST" onsubmit="return confirm('¿Aplicar esta merma al inventario?')">
        @csrf @method('PATCH')
        <button class="btn btn-dp">Aplicar merma</button>
    </form>
    <form action="{{ route('inventory.shrinkages.cancel', $shrinkage) }}" method="POST" onsubmit="return confirm('¿Cancelar esta merma?')">
        @csrf @method('PATCH')
        <button class="btn btn-outline-danger">Cancelar</button>
    </form>
</div>
@endif
@endsection
