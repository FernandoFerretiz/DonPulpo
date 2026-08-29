<div id="categoryFormErrors"></div>

<form id="categoryEditForm" action="{{ route('dish-categories.update', $category) }}" method="POST">
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
