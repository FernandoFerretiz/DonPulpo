@php($t = $table ?? null)
<div class="mb-3">
    <label class="form-label fw-semibold">Nombre</label>
    <input type="text" name="name" class="form-control" value="{{ old('name', $t?->name) }}"
           placeholder="Ej: Mesa 1" required maxlength="50" />
</div>
<div class="mb-3">
    <label class="form-label fw-semibold">Capacidad (personas)</label>
    <input type="number" name="capacity" class="form-control" value="{{ old('capacity', $t?->capacity ?? 4) }}"
           min="1" max="255" required />
</div>
<div class="mb-3">
    <label class="form-label fw-semibold">Orden de aparición</label>
    <input type="number" name="display_order" class="form-control" value="{{ old('display_order', $t?->display_order ?? 0) }}"
           min="0" />
</div>
<div class="mb-4 form-check">
    <input type="checkbox" name="active" value="1" class="form-check-input" id="activeCheck"
           {{ old('active', $t?->active ?? true) ? 'checked' : '' }} />
    <label class="form-check-label" for="activeCheck">Visible en el selector de la app</label>
</div>
