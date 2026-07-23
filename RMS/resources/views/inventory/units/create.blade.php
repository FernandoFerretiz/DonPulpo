@extends('layouts.app')
@section('title', 'Nueva unidad de medida — Don Pulpo RMS')
@section('content')
<div class="d-flex align-items-center gap-2 mb-3 mt-2">
    <a href="{{ route('inventory.units.index') }}" class="btn btn-sm btn-outline-secondary">← Volver</a>
    <h2 class="h4 mb-0">Nueva unidad de medida</h2>
</div>

<div class="card shadow-sm border-0" style="max-width:520px">
    <div class="card-body">
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <form action="{{ route('inventory.units.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-semibold">Nombre</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="Ej. Kilogramo" required />
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Abreviatura</label>
                <input type="text" name="abbreviation" class="form-control" value="{{ old('abbreviation') }}" placeholder="Ej. kg" required />
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Unidad base <small class="text-muted">(dejar vacío si esta ES la unidad base, ej. gramo)</small></label>
                <select name="base_unit_id" class="form-select">
                    <option value="">— Es unidad base —</option>
                    @foreach($units as $u)
                        <option value="{{ $u->id }}" {{ (string) old('base_unit_id') === (string) $u->id ? 'selected' : '' }}>{{ $u->name }} ({{ $u->abbreviation }})</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="form-label fw-semibold">Factor de conversión a la unidad base</label>
                <input type="number" step="0.0001" min="0.0001" name="conversion_factor" class="form-control" value="{{ old('conversion_factor', 1) }}" required />
                <small class="text-muted">Ej. si la unidad base es "gramo", el kilogramo lleva factor 1000 (1 kg = 1000 g).</small>
            </div>
            <button type="submit" class="btn btn-dp w-100">Crear unidad</button>
        </form>
    </div>
</div>
@endsection
