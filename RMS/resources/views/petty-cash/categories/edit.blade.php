@extends('layouts.app')

@section('title', 'Editar Categoría — Don Pulpo RMS')

@section('content')
<div class="row justify-content-center mt-4">
    <div class="col-lg-5">
        <div class="d-flex align-items-center gap-2 mb-3">
            <a href="{{ route('petty-cash.categories.index') }}" class="btn btn-outline-secondary btn-sm">&larr; Volver</a>
            <h4 class="fw-bold mb-0">Editar Categoría</h4>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <form method="POST" action="{{ route('petty-cash.categories.update', $category) }}">
                    @csrf @method('PUT')
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $category->name) }}" required
                               class="form-control @error('name') is-invalid @enderror">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="isActive"
                                   value="1" {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="isActive">Categoría activa</label>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-dp px-4">Guardar</button>
                        <a href="{{ route('petty-cash.categories.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
