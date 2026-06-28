@extends('layouts.app')

@section('title', 'Nueva Categoría — Don Pulpo RMS')

@section('content')
<div class="row justify-content-center mt-4">
    <div class="col-lg-5">
        <div class="d-flex align-items-center gap-2 mb-3">
            <a href="{{ route('petty-cash.categories.index') }}" class="btn btn-outline-secondary btn-sm">&larr; Volver</a>
            <h4 class="fw-bold mb-0">Nueva Categoría</h4>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <form method="POST" action="{{ route('petty-cash.categories.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                               class="form-control @error('name') is-invalid @enderror"
                               placeholder="Ej. Materiales de limpieza">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-dp px-4">Crear</button>
                        <a href="{{ route('petty-cash.categories.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
