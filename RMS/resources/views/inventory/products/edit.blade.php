@extends('layouts.app')
@section('title', 'Editar producto de inventario — Don Pulpo RMS')
@section('content')
<div class="d-flex align-items-center gap-2 mb-3 mt-2">
    <a href="{{ route('inventory.products.index') }}" class="btn btn-sm btn-outline-secondary">← Volver</a>
    <h2 class="h4 mb-0">Editar: {{ $product->name }}</h2>
</div>

@php $primarySupplierId = $product->suppliers->firstWhere('pivot.is_primary', true)?->id; @endphp

<div class="card shadow-sm border-0" style="max-width:680px">
    <div class="card-body">
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <form action="{{ route('inventory.products.update', $product) }}" method="POST">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label fw-semibold">Nombre</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $product->name) }}" required />
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Código interno</label>
                    <input type="text" name="internal_code" class="form-control" value="{{ old('internal_code', $product->internal_code) }}" required />
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Código de barras</label>
                    <input type="text" name="barcode" class="form-control" value="{{ old('barcode', $product->barcode) }}" />
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Categoría</label>
                    <select name="inventory_category_id" class="form-select">
                        <option value="">— Sin categoría —</option>
                        @foreach($categories as $c)
                            <option value="{{ $c->id }}" {{ (string) old('inventory_category_id', $product->inventory_category_id) === (string) $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Unidad de medida</label>
                    <select name="unit_of_measure_id" class="form-select" required>
                        @foreach($units as $u)
                            <option value="{{ $u->id }}" {{ (string) old('unit_of_measure_id', $product->unit_of_measure_id) === (string) $u->id ? 'selected' : '' }}>{{ $u->name }} ({{ $u->abbreviation }})</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Stock mínimo</label>
                    <input type="number" step="0.001" min="0" name="min_stock" class="form-control" value="{{ old('min_stock', $product->min_stock) }}" />
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Stock máximo</label>
                    <input type="number" step="0.001" min="0" name="max_stock" class="form-control" value="{{ old('max_stock', $product->max_stock) }}" />
                </div>
            </div>
            <div class="row">
                <div class="col-md-8 mb-3">
                    <label class="form-label fw-semibold">Proveedor principal</label>
                    <select name="supplier_id" class="form-select">
                        <option value="">— Sin proveedor —</option>
                        @foreach($suppliers as $s)
                            <option value="{{ $s->id }}" {{ (string) old('supplier_id', $primarySupplierId) === (string) $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-semibold">Costo habitual</label>
                    <input type="number" step="0.0001" min="0" name="supplier_cost" class="form-control" value="{{ old('supplier_cost', $product->suppliers->firstWhere('id', $primarySupplierId)?->pivot->cost) }}" />
                </div>
            </div>
            <div class="mb-3">
                <div class="row text-muted small">
                    <div class="col-md-6">Costo promedio: <strong>${{ number_format($product->average_cost, 4) }}</strong></div>
                    <div class="col-md-6">Último costo: <strong>${{ number_format($product->last_cost, 4) }}</strong></div>
                </div>
            </div>
            <div class="mb-4 d-flex flex-wrap gap-4">
                <div class="form-check">
                    <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active" {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Activo</label>
                </div>
                <div class="form-check">
                    <input type="checkbox" name="tracks_inventory" value="1" class="form-check-input" id="tracks_inventory" {{ old('tracks_inventory', $product->tracks_inventory) ? 'checked' : '' }}>
                    <label class="form-check-label" for="tracks_inventory">Maneja inventario</label>
                </div>
                <div class="form-check">
                    <input type="checkbox" name="tracks_lots" value="1" class="form-check-input" id="tracks_lots" {{ old('tracks_lots', $product->tracks_lots) ? 'checked' : '' }}>
                    <label class="form-check-label" for="tracks_lots">Maneja lotes</label>
                </div>
                <div class="form-check">
                    <input type="checkbox" name="tracks_expiration" value="1" class="form-check-input" id="tracks_expiration" {{ old('tracks_expiration', $product->tracks_expiration) ? 'checked' : '' }}>
                    <label class="form-check-label" for="tracks_expiration">Maneja caducidad</label>
                </div>
            </div>
            <button type="submit" class="btn btn-dp w-100">Guardar cambios</button>
        </form>
    </div>
</div>
@endsection
