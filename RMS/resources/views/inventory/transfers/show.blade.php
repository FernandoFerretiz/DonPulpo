@extends('layouts.app')
@section('title', 'Transferencia ' . $transfer->folio . ' — Don Pulpo RMS')
@section('content')
<div class="d-flex align-items-center gap-2 mb-3 mt-2">
    <a href="{{ route('inventory.transfers.index') }}" class="btn btn-sm btn-outline-secondary">← Volver</a>
    <h2 class="h4 mb-0">Transferencia {{ $transfer->folio }}</h2>
    <span class="badge {{ $transfer->status === 'completed' ? 'badge-active' : ($transfer->status === 'cancelled' ? 'badge-inactive' : 'bg-secondary') }}">
        {{ $transfer->getStatusLabel() }}
    </span>
</div>

<div class="row mb-3">
    <div class="col-md-4"><strong>Origen:</strong> {{ $transfer->originWarehouse->name }}</div>
    <div class="col-md-4"><strong>Destino:</strong> {{ $transfer->destinationWarehouse->name }}</div>
    <div class="col-md-4"><strong>Fecha:</strong> {{ $transfer->transfer_date->format('d/m/Y') }}</div>
</div>
@if($transfer->notes)
    <div class="mb-3"><strong>Notas:</strong> {{ $transfer->notes }}</div>
@endif

<div class="card shadow-sm border-0 mb-3">
    <div class="table-responsive">
        <table class="table mb-0">
            <thead class="table-light"><tr><th>Producto</th><th>Cantidad</th></tr></thead>
            <tbody>
                @foreach($transfer->items as $item)
                <tr>
                    <td>{{ $item->product->name }}</td>
                    <td>{{ rtrim(rtrim(number_format($item->quantity, 3), '0'), '.') }} {{ $item->product->unitOfMeasure->abbreviation }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@if($transfer->isDraft())
<div class="d-flex gap-2">
    <form action="{{ route('inventory.transfers.complete', $transfer) }}" method="POST" onsubmit="return confirm('¿Completar esta transferencia?')">
        @csrf @method('PATCH')
        <button class="btn btn-dp">Completar transferencia</button>
    </form>
    <form action="{{ route('inventory.transfers.cancel', $transfer) }}" method="POST" onsubmit="return confirm('¿Cancelar esta transferencia?')">
        @csrf @method('PATCH')
        <button class="btn btn-outline-danger">Cancelar</button>
    </form>
</div>
@endif
@endsection
