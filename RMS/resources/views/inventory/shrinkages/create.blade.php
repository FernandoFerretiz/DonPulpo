@extends('layouts.app')
@section('title', 'Nueva merma — Don Pulpo RMS')
@section('content')
<div class="d-flex align-items-center gap-2 mb-3 mt-2">
    <a href="{{ route('inventory.shrinkages.index') }}" class="btn btn-sm btn-outline-secondary">← Volver</a>
    <h2 class="h4 mb-0">Nueva merma</h2>
</div>

<div class="card shadow-sm border-0" style="max-width:820px">
    <div class="card-body">
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <form action="{{ route('inventory.shrinkages.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-semibold">Almacén</label>
                    <select name="warehouse_id" class="form-select" required>
                        <option value="">Seleccionar…</option>
                        @foreach($warehouses as $w)
                            <option value="{{ $w->id }}" {{ (string) old('warehouse_id') === (string) $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-semibold">Motivo</label>
                    <select name="reason" class="form-select" required>
                        <option value="">Seleccionar…</option>
                        @foreach($reasons as $r)
                            <option value="{{ $r }}" {{ old('reason') === $r ? 'selected' : '' }}>
                                {{ match($r) { 'expired' => 'Caducidad', 'damaged' => 'Daño', 'spillage' => 'Derrame', 'internal_consumption' => 'Consumo interno', default => 'Otro' } }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-semibold">Fecha</label>
                    <input type="date" name="shrinkage_date" class="form-control" value="{{ old('shrinkage_date', now()->format('Y-m-d')) }}" required />
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Observaciones</label>
                <input type="text" name="notes" class="form-control" value="{{ old('notes') }}" />
            </div>

            <hr>
            <div class="d-flex justify-content-between align-items-center mb-2">
                <label class="form-label fw-semibold mb-0">Productos perdidos</label>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="addRow">+ Agregar producto</button>
            </div>
            <table class="table" id="itemsTable">
                <thead class="table-light"><tr><th>Producto</th><th style="width:160px">Cantidad</th><th style="width:40px"></th></tr></thead>
                <tbody id="itemsBody"></tbody>
            </table>
            <small class="text-muted d-block mb-3">El costo se toma automáticamente del costo promedio vigente de cada producto.</small>

            <button type="submit" class="btn btn-dp mt-2">Crear merma (borrador)</button>
        </form>
    </div>
</div>

<template id="rowTemplate">
    <tr>
        <td>
            <select name="items[__INDEX__][inventory_product_id]" class="form-select form-select-sm" required>
                <option value="">Seleccionar…</option>
                @foreach($products as $prod)
                    <option value="{{ $prod->id }}">{{ $prod->name }} ({{ $prod->unitOfMeasure->abbreviation }})</option>
                @endforeach
            </select>
        </td>
        <td><input type="number" step="0.001" min="0.001" name="items[__INDEX__][quantity]" class="form-control form-control-sm" required></td>
        <td><button type="button" class="btn btn-sm btn-outline-danger removeRow">✕</button></td>
    </tr>
</template>

<script>
(function () {
    const body = document.getElementById('itemsBody');
    const template = document.getElementById('rowTemplate');
    let index = 0;

    function addRow() {
        const html = template.innerHTML.replaceAll('__INDEX__', index++);
        const wrapper = document.createElement('tbody');
        wrapper.innerHTML = html;
        const row = wrapper.firstElementChild;
        body.appendChild(row);
        row.querySelector('.removeRow').addEventListener('click', () => row.remove());
    }

    document.getElementById('addRow').addEventListener('click', addRow);
    addRow();
})();
</script>
@endsection
