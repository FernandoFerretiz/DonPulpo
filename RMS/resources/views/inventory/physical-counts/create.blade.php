@extends('layouts.app')
@section('title', 'Nuevo conteo físico — Don Pulpo RMS')
@section('content')
<div class="d-flex align-items-center gap-2 mb-3 mt-2">
    <a href="{{ route('inventory.physical-counts.index') }}" class="btn btn-sm btn-outline-secondary">← Volver</a>
    <h2 class="h4 mb-0">Nuevo conteo físico</h2>
</div>

<div class="card shadow-sm border-0" style="max-width:560px">
    <div class="card-body">
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <form action="{{ route('inventory.physical-counts.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-semibold">Almacén a contar</label>
                <select name="warehouse_id" class="form-select" required>
                    <option value="">Seleccionar…</option>
                    @foreach($warehouses as $w)
                        <option value="{{ $w->id }}" {{ (string) old('warehouse_id') === (string) $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Fecha</label>
                <input type="date" name="count_date" class="form-control" value="{{ old('count_date', now()->format('Y-m-d')) }}" required />
            </div>
            <div class="mb-4">
                <label class="form-label fw-semibold">Observaciones</label>
                <input type="text" name="notes" class="form-control" value="{{ old('notes') }}" />
            </div>
            <button type="submit" class="btn btn-dp w-100">Crear conteo</button>
            <small class="text-muted d-block mt-2">Se precargan todos los productos activos del almacén con su existencia actual del sistema; después capturás la cantidad real de cada uno.</small>
        </form>
    </div>
</div>
@endsection
