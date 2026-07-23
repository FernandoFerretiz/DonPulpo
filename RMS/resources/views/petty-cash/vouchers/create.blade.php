@extends('layouts.app')

@section('title', 'Nuevo Vale — Don Pulpo RMS')

@section('content')
<div class="row justify-content-center mt-4">
    <div class="col-lg-7">
        <div class="d-flex align-items-center gap-2 mb-3">
            <a href="{{ route('petty-cash.vouchers.index') }}" class="btn btn-outline-secondary btn-sm">&larr; Volver</a>
            <h4 class="fw-bold mb-0">Nuevo Vale de Caja Chica</h4>
        </div>

        <div class="card shadow-sm">
            <div class="card-header py-2">
                <span class="fw-bold" style="font-size:.95rem">Solicitud de vale</span>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('petty-cash.vouchers.store') }}">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Categoría</label>
                            <select name="petty_cash_category_id" class="form-select @error('petty_cash_category_id') is-invalid @enderror">
                                <option value="">— Sin categoría —</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('petty_cash_category_id') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('petty_cash_category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Monto <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" name="amount" step="0.01" min="0.01"
                                       class="form-control @error('amount') is-invalid @enderror"
                                       value="{{ old('amount') }}" required placeholder="0.00">
                                @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Concepto / Descripción <span class="text-danger">*</span></label>
                            <textarea name="concept" rows="3"
                                      class="form-control @error('concept') is-invalid @enderror"
                                      required placeholder="Describe para qué se usará el dinero...">{{ old('concept') }}</textarea>
                            @error('concept')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Beneficiario</label>
                            <input type="text" name="beneficiary"
                                   class="form-control @error('beneficiary') is-invalid @enderror"
                                   value="{{ old('beneficiary') }}" placeholder="Nombre de quien recibe el pago">
                            @error('beneficiary')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Notas adicionales</label>
                            <textarea name="notes" rows="2"
                                      class="form-control @error('notes') is-invalid @enderror"
                                      placeholder="Opcional...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-dp px-4">Crear vale</button>
                        <a href="{{ route('petty-cash.vouchers.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
