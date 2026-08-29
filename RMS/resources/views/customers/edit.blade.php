@extends('layouts.app')
@section('title', 'Editar cliente — Don Pulpo RMS')
@section('content')
<div class="d-flex align-items-center gap-2 mb-3 mt-2">
    <a href="{{ route('customers.index') }}" class="btn btn-sm btn-outline-secondary">← Volver</a>
    <h2 class="h4 mb-0">Editar cliente</h2>
</div>

<div class="card shadow-sm border-0" style="max-width:520px">
    <div class="card-body">
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <p class="text-muted">Saldo adeudado actual: <strong>${{ number_format($customer->balance, 2) }}</strong></p>

        <form action="{{ route('customers.update', $customer) }}" method="POST">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label fw-semibold">Nombre</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $customer->name) }}" required />
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Teléfono</label>
                <input type="text" name="phone" class="form-control" value="{{ old('phone', $customer->phone) }}" />
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Límite de crédito <small class="text-muted">(vacío = sin límite)</small></label>
                <input type="number" name="credit_limit" class="form-control" value="{{ old('credit_limit', $customer->credit_limit) }}" min="0" step="0.01" />
            </div>
            <div class="mb-4">
                <label class="form-label fw-semibold">Notas</label>
                <textarea name="notes" class="form-control" rows="3">{{ old('notes', $customer->notes) }}</textarea>
            </div>
            <button type="submit" class="btn btn-dp w-100">Guardar cambios</button>
        </form>
    </div>
</div>
@endsection
