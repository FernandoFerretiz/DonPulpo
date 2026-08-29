<div id="dishFormErrors"></div>

<form id="dishEditForm" action="{{ route('dishes.update', $dish) }}" method="POST" enctype="multipart/form-data">
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

    <div class="mb-4">
        <label class="form-label fw-semibold">Modificadores</label>
        @forelse($modifierGroups as $group)
            @php $checked = collect(old('modifier_group_ids', $selectedGroupIds))->contains($group->id); @endphp
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
                                   value="{{ old("modifier_prices.{$option->id}", $optionPrices[$option->id] ?? 0) }}" placeholder="0.00" />
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="text-muted small">
                No hay grupos de modificadores creados. <a href="{{ route('modifier-groups.create') }}" target="_blank">Crear uno</a>.
            </div>
        @endforelse
    </div>

    <button type="submit" class="btn btn-dp w-100">Guardar cambios</button>
</form>

<script>
(function () {
    const imageInput = document.getElementById('imageInput');
    if (imageInput) {
        imageInput.addEventListener('change', function () {
            const file = this.files[0];
            const wrap = document.getElementById('imagePreviewWrap');
            const img  = document.getElementById('imagePreview');
            if (file) {
                img.src = URL.createObjectURL(file);
                wrap.style.display = 'block';
                const removeChk = document.getElementById('removeImage');
                if (removeChk) removeChk.checked = false;
            } else {
                wrap.style.display = 'none';
            }
        });
    }
    document.querySelectorAll('.group-toggle').forEach(cb => {
        cb.addEventListener('change', function () {
            document.getElementById(this.dataset.target).style.display = this.checked ? 'block' : 'none';
        });
    });
})();
</script>
