@extends('layouts.app')
@section('title', 'Nuevo grupo de modificadores — Don Pulpo RMS')
@section('content')
<div class="d-flex align-items-center gap-2 mb-3 mt-2">
    <a href="{{ route('modifier-groups.index') }}" class="btn btn-sm btn-outline-secondary">← Volver</a>
    <h2 class="h4 mb-0">Nuevo grupo de modificadores</h2>
</div>

<div class="card shadow-sm border-0" style="max-width:640px">
    <div class="card-body">
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <form action="{{ route('modifier-groups.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="Ej: Tamaño, Sabor" required />
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Tipo de selección</label>
                <select name="selection_type" class="form-select">
                    @foreach($selectionTypes as $type)
                        <option value="{{ $type }}" {{ old('selection_type') === $type ? 'selected' : '' }}>
                            {{ $type === 'single' ? 'Selección única' : 'Selección múltiple' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">¿Cómo afecta el precio?</label>
                <select name="pricing_mode" id="pricingMode" class="form-select">
                    <option value="delta" {{ old('pricing_mode', 'delta') === 'delta' ? 'selected' : '' }}>
                        Ajuste sobre el precio del platillo (ej: extra queso +$15)
                    </option>
                    <option value="absolute" {{ old('pricing_mode') === 'absolute' ? 'selected' : '' }}>
                        Cada opción tiene su propio precio final (ej: tamaños CH/MED/GR)
                    </option>
                </select>
            </div>

            <div class="mb-3 form-check">
                <input class="form-check-input" type="checkbox" name="required" id="required" value="1" {{ old('required') ? 'checked' : '' }} />
                <label class="form-check-label" for="required">Obligatorio elegir una opción</label>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Opciones <span class="text-danger">*</span></label>
                <div id="optionsHint" class="form-text mb-1"></div>
                <div id="optionsWrap"></div>
                <button type="button" class="btn btn-sm btn-outline-secondary mt-1" id="addOption">+ Agregar opción</button>
            </div>

            <button type="submit" class="btn btn-dp w-100">Crear grupo</button>
        </form>
    </div>
</div>

<script>
let optionIndex = 0;
function updateHint() {
    const hint = document.getElementById('optionsHint');
    hint.textContent = 'El precio de cada opción se define por platillo, en la pantalla de edición de cada platillo.';
}
function addOptionRow(name = '') {
    const wrap = document.getElementById('optionsWrap');
    const row = document.createElement('div');
    row.className = 'input-group mb-2';
    row.innerHTML = `
        <input type="text" name="options[${optionIndex}][name]" class="form-control" placeholder="Ej: CH, MED, GR" value="${name}" required />
        <button type="button" class="btn btn-outline-danger removeOption">✕</button>
    `;
    wrap.appendChild(row);
    row.querySelector('.removeOption').addEventListener('click', () => row.remove());
    optionIndex++;
}
document.getElementById('addOption').addEventListener('click', () => addOptionRow());
addOptionRow();
addOptionRow();
updateHint();
</script>
@endsection
