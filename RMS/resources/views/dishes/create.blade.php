@extends('layouts.app')
@section('title', 'Nuevo platillo — Don Pulpo RMS')
@section('content')
<div class="d-flex align-items-center gap-2 mb-3 mt-2">
    <a href="{{ route('dishes.index') }}" class="btn btn-sm btn-outline-secondary">← Volver</a>
    <h2 class="h4 mb-0">Nuevo platillo</h2>
</div>

<div class="card shadow-sm border-0" style="max-width:640px">
    <div class="card-body">
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <form action="{{ route('dishes.store') }}" method="POST" enctype="multipart/form-data">
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
                <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required />
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Descripción <small class="text-muted">(ingredientes, alérgenos, etc.)</small></label>
                <textarea name="description" class="form-control" rows="3"
                    placeholder="Ej: Camarones frescos, jitomate, cebolla, cilantro, limón. Contiene mariscos.">{{ old('description') }}</textarea>
                <div class="form-text">Esta descripción se mostrará al cliente en el menú.</div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Foto del platillo <small class="text-muted">(JPG, PNG o WebP, máx. 3 MB)</small></label>
                <input type="file" name="image" id="imageInput" class="form-control" accept="image/jpeg,image/png,image/webp" />
                <div id="imagePreviewWrap" class="mt-2" style="display:none;">
                    <img id="imagePreview" src="" alt="Vista previa"
                         style="max-height:180px;border-radius:12px;object-fit:cover;border:1px solid #dee2e6;" />
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Precio <span class="text-danger">*</span></label>
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

            <div class="mb-4">
                <label class="form-label fw-semibold">Modificadores</label>
                @forelse($modifierGroups as $group)
                    @php $checked = collect(old('modifier_group_ids'))->contains($group->id); @endphp
                    <div class="border rounded p-2 mb-2">
                        <div class="form-check">
                            <input class="form-check-input group-toggle" type="checkbox" name="modifier_group_ids[]" value="{{ $group->id }}"
                                   id="mg{{ $group->id }}" data-target="group-options-{{ $group->id }}" {{ $checked ? 'checked' : '' }} />
                            <label class="form-check-label fw-semibold" for="mg{{ $group->id }}">
                                {{ $group->name }}
                                <small class="text-muted">({{ $group->pricing_mode === 'absolute' ? 'precio por opción' : 'ajuste sobre el precio' }})</small>
                            </label>
                        </div>
                        <div id="group-options-{{ $group->id }}" style="display:{{ $checked ? 'block' : 'none' }}" class="mt-2 ps-4">
                            @foreach($group->options as $option)
                                <div class="input-group input-group-sm mb-1" style="max-width:280px">
                                    <span class="input-group-text" style="min-width:80px">{{ $option->name }}</span>
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" name="modifier_prices[{{ $option->id }}]" class="form-control"
                                           value="{{ old("modifier_prices.{$option->id}", 0) }}" placeholder="0.00" />
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="text-muted small">
                        No hay grupos de modificadores creados. <a href="{{ route('modifier-groups.create') }}">Crear uno</a>.
                    </div>
                @endforelse
            </div>

            <button type="submit" class="btn btn-dp w-100">Crear platillo</button>
        </form>
    </div>
</div>

<script>
document.getElementById('imageInput').addEventListener('change', function () {
    const file = this.files[0];
    const wrap = document.getElementById('imagePreviewWrap');
    const img  = document.getElementById('imagePreview');
    if (file) {
        img.src = URL.createObjectURL(file);
        wrap.style.display = 'block';
    } else {
        wrap.style.display = 'none';
    }
});
document.querySelectorAll('.group-toggle').forEach(cb => {
    cb.addEventListener('change', function () {
        document.getElementById(this.dataset.target).style.display = this.checked ? 'block' : 'none';
    });
});
</script>
@endsection
