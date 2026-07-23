@extends('layouts.app')
@section('title', 'Nueva compra — Don Pulpo RMS')
@section('content')
<div class="d-flex align-items-center gap-2 mb-3 mt-2">
    <a href="{{ route('inventory.purchases.index') }}" class="btn btn-sm btn-outline-secondary">← Volver</a>
    <h2 class="h4 mb-0">Nueva compra</h2>
</div>

<div class="card shadow-sm border-0" style="max-width:900px">
    <div class="card-body">
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <form action="{{ route('inventory.purchases.store') }}" method="POST" id="purchaseForm">
            @csrf
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-semibold">Proveedor</label>
                    <select name="supplier_id" class="form-select" required>
                        <option value="">Seleccionar…</option>
                        @foreach($suppliers as $s)
                            <option value="{{ $s->id }}" {{ (string) old('supplier_id') === (string) $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-semibold">Almacén destino</label>
                    <select name="warehouse_id" class="form-select" required>
                        <option value="">Seleccionar…</option>
                        @foreach($warehouses as $w)
                            <option value="{{ $w->id }}" {{ (string) old('warehouse_id') === (string) $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-semibold">Fecha</label>
                    <input type="date" name="purchase_date" class="form-control" value="{{ old('purchase_date', now()->format('Y-m-d')) }}" required />
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Factura <small class="text-muted">(opcional)</small></label>
                    <input type="text" name="invoice_number" class="form-control" value="{{ old('invoice_number') }}" />
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Observaciones</label>
                    <input type="text" name="notes" class="form-control" value="{{ old('notes') }}" />
                </div>
            </div>

            <hr>
            <div class="d-flex justify-content-between align-items-center mb-2">
                <label class="form-label fw-semibold mb-0">Productos</label>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="addRow">+ Agregar producto</button>
            </div>
            <div class="table-responsive">
                <table class="table" id="itemsTable">
                    <thead class="table-light">
                        <tr>
                            <th style="min-width:220px">Producto</th>
                            <th style="width:140px">Cantidad</th>
                            <th style="width:160px">Costo unitario</th>
                            <th style="width:140px">Subtotal</th>
                            <th style="width:40px"></th>
                        </tr>
                    </thead>
                    <tbody id="itemsBody"></tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-end fw-semibold">Total</td>
                            <td id="grandTotal" class="fw-semibold">$0.00</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <button type="submit" class="btn btn-dp mt-3">Crear compra (borrador)</button>
            <small class="text-muted d-block mt-2">La compra se crea en borrador; las existencias y costos se actualizan al recibirla.</small>
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
        <td><input type="number" step="0.001" min="0.001" name="items[__INDEX__][quantity]" class="form-control form-control-sm qty" required></td>
        <td><input type="number" step="0.0001" min="0" name="items[__INDEX__][unit_cost]" class="form-control form-control-sm cost" required></td>
        <td class="subtotal">$0.00</td>
        <td><button type="button" class="btn btn-sm btn-outline-danger removeRow">✕</button></td>
    </tr>
</template>

<script>
(function () {
    const body = document.getElementById('itemsBody');
    const template = document.getElementById('rowTemplate');
    let index = 0;

    function recalcRow(row) {
        const qty = parseFloat(row.querySelector('.qty').value) || 0;
        const cost = parseFloat(row.querySelector('.cost').value) || 0;
        row.querySelector('.subtotal').textContent = '$' + (qty * cost).toFixed(2);
        recalcTotal();
    }

    function recalcTotal() {
        let total = 0;
        body.querySelectorAll('tr').forEach(row => {
            const qty = parseFloat(row.querySelector('.qty').value) || 0;
            const cost = parseFloat(row.querySelector('.cost').value) || 0;
            total += qty * cost;
        });
        document.getElementById('grandTotal').textContent = '$' + total.toFixed(2);
    }

    function addRow() {
        const html = template.innerHTML.replaceAll('__INDEX__', index++);
        const wrapper = document.createElement('tbody');
        wrapper.innerHTML = html;
        const row = wrapper.firstElementChild;
        body.appendChild(row);
        row.querySelectorAll('.qty, .cost').forEach(el => el.addEventListener('input', () => recalcRow(row)));
        row.querySelector('.removeRow').addEventListener('click', () => { row.remove(); recalcTotal(); });
    }

    document.getElementById('addRow').addEventListener('click', addRow);
    addRow();
})();
</script>
@endsection
