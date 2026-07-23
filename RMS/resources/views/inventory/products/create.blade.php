@extends('layouts.app')
@section('title', 'Nuevo producto de inventario — Don Pulpo RMS')
@section('content')
<div class="d-flex align-items-center gap-2 mb-3 mt-2">
    <a href="{{ route('inventory.products.index') }}" class="btn btn-sm btn-outline-secondary">← Volver</a>
    <h2 class="h4 mb-0">Nuevo producto de inventario</h2>
</div>

<div class="card shadow-sm border-0" style="max-width:680px">
    <div class="card-body">
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <form action="{{ route('inventory.products.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-semibold">Nombre</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required />
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Código interno <small class="text-muted">(se genera automáticamente si se deja vacío)</small></label>
                    <input type="text" name="internal_code" class="form-control" value="{{ old('internal_code') }}" />
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Código de barras <small class="text-muted">(opcional)</small></label>
                    <input type="text" name="barcode" class="form-control" value="{{ old('barcode') }}" />
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Categoría</label>
                    <select name="inventory_category_id" class="form-select">
                        <option value="">— Sin categoría —</option>
                        @foreach($categories as $c)
                            <option value="{{ $c->id }}" {{ (string) old('inventory_category_id') === (string) $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Unidad de medida</label>
                    <select name="unit_of_measure_id" class="form-select" required>
                        <option value="">Seleccionar…</option>
                        @foreach($units as $u)
                            <option value="{{ $u->id }}" {{ (string) old('unit_of_measure_id') === (string) $u->id ? 'selected' : '' }}>{{ $u->name }} ({{ $u->abbreviation }})</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Stock mínimo</label>
                    <input type="number" step="0.001" min="0" name="min_stock" class="form-control" value="{{ old('min_stock', 0) }}" />
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Stock máximo</label>
                    <input type="number" step="0.001" min="0" name="max_stock" class="form-control" value="{{ old('max_stock', 0) }}" />
                </div>
            </div>
            <div class="row">
                <div class="col-md-8 mb-3">
                    <label class="form-label fw-semibold">Proveedor principal <small class="text-muted">(opcional)</small></label>
                    <select name="supplier_id" class="form-select">
                        <option value="">— Sin proveedor —</option>
                        @foreach($suppliers as $s)
                            <option value="{{ $s->id }}" {{ (string) old('supplier_id') === (string) $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-semibold">Costo habitual</label>
                    <input type="number" step="0.0001" min="0" name="supplier_cost" class="form-control" value="{{ old('supplier_cost') }}" />
                </div>
            </div>
            <div class="mb-4 d-flex flex-wrap gap-4">
                <div class="form-check">
                    <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active" {{ old('is_active', true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Activo</label>
                </div>
                <div class="form-check">
                    <input type="checkbox" name="tracks_inventory" value="1" class="form-check-input" id="tracks_inventory" {{ old('tracks_inventory', true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="tracks_inventory">Maneja inventario</label>
                </div>
                <div class="form-check">
                    <input type="checkbox" name="tracks_lots" value="1" class="form-check-input" id="tracks_lots" {{ old('tracks_lots') ? 'checked' : '' }}>
                    <label class="form-check-label" for="tracks_lots">Maneja lotes</label>
                </div>
                <div class="form-check">
                    <input type="checkbox" name="tracks_expiration" value="1" class="form-check-input" id="tracks_expiration" {{ old('tracks_expiration') ? 'checked' : '' }}>
                    <label class="form-check-label" for="tracks_expiration">Maneja caducidad</label>
                </div>
            </div>
            <button type="submit" class="btn btn-dp w-100">Crear producto</button>
        </form>
    </div>
</div>
@endsection
