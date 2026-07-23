@extends('layouts.app')
@section('title', 'Conteo ' . $count->folio . ' — Don Pulpo RMS')
@section('content')
<div class="d-flex align-items-center gap-2 mb-3 mt-2">
    <a href="{{ route('inventory.physical-counts.index') }}" class="btn btn-sm btn-outline-secondary">← Volver</a>
    <h2 class="h4 mb-0">Conteo {{ $count->folio }}</h2>
    <span class="badge {{ $count->status === 'confirmed' ? 'badge-active' : ($count->status === 'cancelled' ? 'badge-inactive' : 'bg-secondary') }}">
        {{ $count->getStatusLabel() }}
    </span>
</div>

<div class="row mb-3">
    <div class="col-md-4"><strong>Almacén:</strong> {{ $count->warehouse->name }}</div>
    <div class="col-md-4"><strong>Fecha:</strong> {{ $count->count_date->format('d/m/Y') }}</div>
    @if($count->adjustment)
        <div class="col-md-4"><strong>Ajuste generado:</strong> <code>{{ $count->adjustment->folio }}</code></div>
    @endif
</div>
@if($count->notes)
    <div class="mb-3"><strong>Notas:</strong> {{ $count->notes }}</div>
@endif

@if($count->isOpen())
<form action="{{ route('inventory.physical-counts.capture', $count) }}" method="POST">
    @csrf @method('PATCH')
    <div class="card shadow-sm border-0 mb-3">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead class="table-light">
                    <tr><th>Producto</th><th>Existencia sistema</th><th style="width:180px">Cantidad real</th><th>Diferencia</th></tr>
                </thead>
                <tbody>
                    @foreach($count->items as $item)
                    <tr>
                        <td>{{ $item->product->name }}</td>
                        <td>{{ rtrim(rtrim(number_format($item->system_quantity, 3), '0'), '.') }} {{ $item->product->unitOfMeasure->abbreviation }}</td>
                        <td>
                            <input type="number" step="0.001" min="0"
                                   name="items[{{ $item->id }}][counted_quantity]"
                                   class="form-control form-control-sm"
                                   value="{{ old('items.' . $item->id . '.counted_quantity', $item->counted_quantity) }}">
                        </td>
                        <td>
                            @if(!is_null($item->difference))
                                <span class="{{ $item->difference >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $item->difference >= 0 ? '+' : '' }}{{ rtrim(rtrim(number_format($item->difference, 3), '0'), '.') }}
                                </span>
                            @else
                                <span class="text-muted">— sin capturar —</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-outline-secondary">Guardar cantidades capturadas</button>
    </div>
</form>

<form action="{{ route('inventory.physical-counts.confirm', $count) }}" method="POST" class="d-inline"
      onsubmit="return confirm('¿Confirmar este conteo? Se generará y aplicará un ajuste de inventario automáticamente por las diferencias encontradas.')">
    @csrf @method('PATCH')
    <button class="btn btn-dp mt-2">Confirmar conteo y generar ajuste</button>
</form>
<form action="{{ route('inventory.physical-counts.cancel', $count) }}" method="POST" class="d-inline"
      onsubmit="return confirm('¿Cancelar este conteo?')">
    @csrf @method('PATCH')
    <button class="btn btn-outline-danger mt-2">Cancelar conteo</button>
</form>
@else
<div class="card shadow-sm border-0 mb-3">
    <div class="table-responsive">
        <table class="table mb-0">
            <thead class="table-light">
                <tr><th>Producto</th><th>Sistema</th><th>Real</th><th>Diferencia</th></tr>
            </thead>
            <tbody>
                @foreach($count->items as $item)
                <tr>
                    <td>{{ $item->product->name }}</td>
                    <td>{{ rtrim(rtrim(number_format($item->system_quantity, 3), '0'), '.') }}</td>
                    <td>{{ rtrim(rtrim(number_format($item->counted_quantity, 3), '0'), '.') }}</td>
                    <td class="{{ $item->difference >= 0 ? 'text-success' : 'text-danger' }}">
                        {{ $item->difference >= 0 ? '+' : '' }}{{ rtrim(rtrim(number_format($item->difference, 3), '0'), '.') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection
