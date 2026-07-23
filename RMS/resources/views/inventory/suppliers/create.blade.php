@extends('layouts.app')
@section('title', 'Nuevo proveedor — Don Pulpo RMS')
@section('content')
<div class="d-flex align-items-center gap-2 mb-3 mt-2">
    <a href="{{ route('inventory.suppliers.index') }}" class="btn btn-sm btn-outline-secondary">← Volver</a>
    <h2 class="h4 mb-0">Nuevo proveedor</h2>
</div>

<div class="card shadow-sm border-0" style="max-width:560px">
    <div class="card-body">
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <form action="{{ route('inventory.suppliers.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-semibold">Nombre</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required />
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Contacto</label>
                <input type="text" name="contact_name" class="form-control" value="{{ old('contact_name') }}" />
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Teléfono</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" />
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" />
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Dirección</label>
                <input type="text" name="address" class="form-control" value="{{ old('address') }}" />
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
            <button type="submit" class="btn btn-dp w-100">Crear proveedor</button>
        </form>
    </div>
</div>
@endsection
