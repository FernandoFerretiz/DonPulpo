@extends('layouts.app')
@section('title', 'Compra ' . $purchase->folio . ' — Don Pulpo RMS')
@section('content')
<div class="d-flex align-items-center gap-2 mb-3 mt-2">
    <a href="{{ route('inventory.purchases.index') }}" class="btn btn-sm btn-outline-secondary">← Volver</a>
    <h2 class="h4 mb-0">Compra {{ $purchase->folio }}</h2>
    <span class="badge {{ $purchase->status === 'received' ? 'badge-active' : ($purchase->status === 'cancelled' ? 'badge-inactive' : 'bg-secondary') }}">
        {{ $purchase->getStatusLabel() }}
    </span>
</div>

<div class="row mb-3">
    <div class="col-md-3"><strong>Proveedor:</strong> {{ $purchase->supplier->name }}</div>
    <div class="col-md-3"><strong>Almacén:</strong> {{ $purchase->warehouse->name }}</div>
    <div class="col-md-3"><strong>Factura:</strong> {{ $purchase->invoice_number ?? '—' }}</div>
    <div class="col-md-3"><strong>Fecha:</strong> {{ $purchase->purchase_date->format('d/m/Y') }}</div>
</div>
@if($purchase->notes)
    <div class="mb-3"><strong>Notas:</strong> {{ $purchase->notes }}</div>
@endif

<div class="card shadow-sm border-0 mb-3">
    <div class="table-responsive">
        <table class="table mb-0">
            <thead class="table-light">
                <tr><th>Producto</th><th>Cantidad</th><th>Costo unit.</th><th>Subtotal</th></tr>
            </thead>
            <tbody>
                @foreach($purchase->items as $item)
                <tr>
                    <td>{{ $item->product->name }}</td>
                    <td>{{ rtrim(rtrim(number_format($item->quantity, 3), '0'), '.') }} {{ $item->product->unitOfMeasure->abbreviation }}</td>
                    <td>${{ number_format($item->unit_cost, 4) }}</td>
                    <td>${{ number_format($item->subtotal, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="text-end fw-semibold">Total</td>
                    <td class="fw-semibold">${{ number_format($purchase->items->sum('subtotal'), 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

@if($purchase->isDraft())
<div class="d-flex gap-2">
    <form action="{{ route('inventory.purchases.receive', $purchase) }}" method="POST" onsubmit="return confirm('¿Recibir esta compra? Se actualizarán existencias y costos.')">
        @csrf @method('PATCH')
        <button class="btn btn-dp">Recibir compra</button>
    </form>
    <form action="{{ route('inventory.purchases.cancel', $purchase) }}" method="POST" onsubmit="return confirm('¿Cancelar esta compra?')">
        @csrf @method('PATCH')
        <button class="btn btn-outline-danger">Cancelar</button>
    </form>
</div>
@endif
@endsection
