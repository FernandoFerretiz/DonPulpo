@extends('layouts.app')
@section('title', 'Nuevo platillo — Don Pulpo RMS')
@section('content')
<div class="d-flex align-items-center gap-2 mb-3 mt-2">
    <a href="{{ route('dishes.index') }}" class="btn btn-sm btn-outline-secondary">← Volver</a>
    <h2 class="h4 mb-0">Nuevo platillo</h2>
</div>

<div class="card shadow-sm border-0" style="max-width:600px">
    <div class="card-body">
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <form action="{{ route('dishes.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-semibold">Categoría</label>
                <select name="dish_category_id" class="form-select">
                    <option value="">— Sin categoría —</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('dish_category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Nombre</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required />
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Descripción <small class="text-muted">(opcional)</small></label>
                <textarea name="description" class="form-control" rows="2">{{ old('description') }}</textarea>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Ruta de imagen <small class="text-muted">(opcional, texto)</small></label>
                <input type="text" name="image_path" class="form-control" value="{{ old('image_path') }}" />
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Precio</label>
                <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input type="number" name="price" class="form-control" value="{{ old('price') }}" min="0" step="0.01" required />
                </div>
            </div>
            <div class="mb-4">
                <label class="form-label fw-semibold">Estado</label>
                <select name="status" class="form-select" required>
                    @foreach($statuses as $s)
                        <option value="{{ $s }}" {{ old('status', 'active') === $s ? 'selected' : '' }}>
                            {{ match($s) { 'active' => 'Activo', 'temporarily_inactive' => 'Temporalmente inactivo', default => 'Inactivo' } }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-dp w-100">Crear platillo</button>
        </form>
    </div>
</div>
@endsection
