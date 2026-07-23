@extends('layouts.app')
@section('title', 'Nueva categoría de inventario — Don Pulpo RMS')
@section('content')
<div class="d-flex align-items-center gap-2 mb-3 mt-2">
    <a href="{{ route('inventory.categories.index') }}" class="btn btn-sm btn-outline-secondary">← Volver</a>
    <h2 class="h4 mb-0">Nueva categoría de inventario</h2>
</div>

<div class="card shadow-sm border-0" style="max-width:520px">
    <div class="card-body">
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <form action="{{ route('inventory.categories.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-semibold">Nombre</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required />
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Slug <small class="text-muted">(se genera automáticamente si se deja vacío)</small></label>
                <input type="text" name="slug" class="form-control" value="{{ old('slug') }}" />
            </div>
            <div class="mb-4">
                <label class="form-label fw-semibold">Estado</label>
                <select name="status" class="form-select" required>
                    @foreach($statuses as $s)
                        <option value="{{ $s }}" {{ old('status', 'active') === $s ? 'selected' : '' }}>
                            {{ $s === 'active' ? 'Activa' : 'Inactiva' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-dp w-100">Crear categoría</button>
        </form>
    </div>
</div>
@endsection
