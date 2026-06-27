@extends('layouts.app')
@section('title', 'Editar categoría — Don Pulpo RMS')
@section('content')
<div class="d-flex align-items-center gap-2 mb-3 mt-2">
    <a href="{{ route('dish-categories.index') }}" class="btn btn-sm btn-outline-secondary">← Volver</a>
    <h2 class="h4 mb-0">Editar: {{ $category->name }}</h2>
</div>

<div class="card shadow-sm border-0" style="max-width:520px">
    <div class="card-body">
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <form action="{{ route('dish-categories.update', $category) }}" method="POST">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label fw-semibold">Nombre</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $category->name) }}" required />
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Slug</label>
                <input type="text" name="slug" class="form-control" value="{{ old('slug', $category->slug) }}" />
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Orden de visualización</label>
                <input type="number" name="display_order" class="form-control" value="{{ old('display_order', $category->display_order) }}" min="0" />
            </div>
            <div class="mb-4">
                <label class="form-label fw-semibold">Estado</label>
                <select name="status" class="form-select" required>
                    @foreach($statuses as $s)
                        <option value="{{ $s }}" {{ old('status', $category->status) === $s ? 'selected' : '' }}>
                            {{ $s === 'active' ? 'Activa' : 'Inactiva' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-dp w-100">Guardar cambios</button>
        </form>
    </div>
</div>
@endsection
