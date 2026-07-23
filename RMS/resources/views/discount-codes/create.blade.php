@extends('layouts.app')
@section('title', 'Nuevo código de descuento — Don Pulpo RMS')
@section('content')
<div class="d-flex align-items-center gap-2 mb-3 mt-2">
    <a href="{{ route('discount-codes.index') }}" class="btn btn-sm btn-outline-secondary">← Volver</a>
    <h2 class="h4 mb-0">Nuevo código de descuento</h2>
</div>

<div class="card shadow-sm border-0" style="max-width:520px">
    <div class="card-body">
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <form action="{{ route('discount-codes.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-semibold">Código</label>
                <input type="text" name="code" class="form-control text-uppercase" value="{{ old('code') }}"
                       placeholder="Ej: PROMO10" required maxlength="50" />
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Porcentaje de descuento</label>
                <div class="input-group">
                    <input type="number" name="percentage" class="form-control" value="{{ old('percentage') }}"
                           min="0.01" max="100" step="0.01" required />
                    <span class="input-group-text">%</span>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Nombre del beneficiario</label>
                <input type="text" name="beneficiary_name" class="form-control" value="{{ old('beneficiary_name') }}" required />
            </div>
            <div class="mb-4">
                <label class="form-label fw-semibold">Estado</label>
                <select name="status" class="form-select" required>
                    @foreach($statuses as $s)
                        <option value="{{ $s }}" {{ old('status', 'active') === $s ? 'selected' : '' }}>
                            {{ $s === 'active' ? 'Activo' : 'Inactivo' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-dp w-100">Crear código</button>
        </form>
    </div>
</div>
@endsection
