@extends('layouts.app')
@section('title', 'Editar platillo — Don Pulpo RMS')
@section('content')
<div class="d-flex align-items-center gap-2 mb-3 mt-2">
    <a href="{{ route('dishes.index') }}" class="btn btn-sm btn-outline-secondary">← Volver</a>
    <h2 class="h4 mb-0">Editar: {{ $dish->name }}</h2>
</div>

<div class="card shadow-sm border-0" style="max-width:640px">
    <div class="card-body">
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <form action="{{ route('dishes.update', $dish) }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')

            <div class="mb-3">
                <label class="form-label fw-semibold">Categoría</label>
                <select name="dish_category_id" class="form-select">
                    <option value="">— Sin categoría —</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('dish_category_id', $dish->dish_category_id) == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $dish->name) }}" required />
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Descripción <small class="text-muted">(ingredientes, alérgenos, etc.)</small></label>
                <textarea name="description" class="form-control" rows="3"
                    placeholder="Ej: Camarones frescos, jitomate, cebolla, cilantro, limón. Contiene mariscos.">{{ old('description', $dish->description) }}</textarea>
                <div class="form-text">Esta descripción se mostrará al cliente en el menú.</div>
            </div>

            {{-- ── Foto actual ── --}}
            <div class="mb-3">
                <label class="form-label fw-semibold">Foto del platillo</label>

                @if($dish->image_path)
                    <div class="mb-2 d-flex align-items-start gap-3" id="currentImageWrap">
                        <img src="{{ asset('storage/' . $dish->image_path) }}" alt="{{ $dish->name }}"
                             style="width:120px;height:90px;object-fit:cover;border-radius:10px;border:1px solid #dee2e6;" />
                        <div>
                            <div class="text-muted small mb-2">Foto actual</div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remove_image" id="removeImage" value="1"
                                       {{ old('remove_image') ? 'checked' : '' }} />
                                <label class="form-check-label text-danger small fw-semibold" for="removeImage">
                                    Eliminar foto actual
                                </label>
                            </div>
                        </div>
                    </div>
                @endif

                <input type="file" name="image" id="imageInput" class="form-control" accept="image/jpeg,image/png,image/webp" />
                <div class="form-text">JPG, PNG o WebP, máx. 3 MB. Sube una nueva foto para reemplazar la actual.</div>

                <div id="imagePreviewWrap" class="mt-2" style="display:none;">
                    <div class="text-muted small mb-1">Nueva foto (vista previa):</div>
                    <img id="imagePreview" src="" alt="Vista previa"
                         style="max-height:180px;border-radius:12px;object-fit:cover;border:1px solid #dee2e6;" />
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Precio <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input type="number" name="price" class="form-control" value="{{ old('price', $dish->price) }}" min="0" step="0.01" required />
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Estado</label>
                <select name="status" class="form-select" required>
                    @foreach($statuses as $s)
                        <option value="{{ $s }}" {{ old('status', $dish->status) === $s ? 'selected' : '' }}>
                            {{ match($s) { 'active' => 'Activo', 'temporarily_inactive' => 'Temporalmente inactivo', default => 'Inactivo' } }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-dp w-100">Guardar cambios</button>
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
        // Si elige nueva foto, desmarcar "eliminar"
        const removeChk = document.getElementById('removeImage');
        if (removeChk) removeChk.checked = false;
    } else {
        wrap.style.display = 'none';
    }
});
</script>
@endsection
