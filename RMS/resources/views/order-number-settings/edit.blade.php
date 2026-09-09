@extends('layouts.app')
@section('title', 'Numeración de órdenes — Don Pulpo RMS')
@section('content')
<div class="d-flex align-items-center gap-2 mb-3 mt-2">
    <h2 class="h4 mb-0">Numeración de órdenes</h2>
</div>

<div class="card shadow-sm border-0" style="max-width:520px">
    <div class="card-body">
        @if(session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <form action="{{ route('order-number-settings.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label fw-semibold">Sucursal</label>
                <input type="text" class="form-control" value="{{ \App\Models\PosOrderNumberSetting::BRANCH_NUMBER }}" disabled />
                <div class="form-text">Prefijo fijo del número de orden. Solo hay una sucursal por ahora.</div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Modo de numeración</label>
                <select name="mode" id="mode" class="form-select">
                    <option value="sequential" {{ old('mode', $setting->mode) === 'sequential' ? 'selected' : '' }}>Secuencial (número consecutivo)</option>
                    <option value="random" {{ old('mode', $setting->mode) === 'random' ? 'selected' : '' }}>Aleatorio</option>
                </select>
            </div>

            <div class="mb-4" id="next-number-field">
                <label class="form-label fw-semibold">Siguiente número</label>
                <input type="number" name="next_number" class="form-control" value="{{ old('next_number', $setting->next_number) }}" min="1" max="999999" />
                <div class="form-text">Solo aplica en modo secuencial. Se usará para la próxima orden y luego avanza automáticamente.</div>
            </div>

            <button type="submit" class="btn btn-dp w-100">Guardar</button>
        </form>
    </div>
</div>

<script>
    document.getElementById('mode').addEventListener('change', function () {
        document.getElementById('next-number-field').style.display = this.value === 'sequential' ? '' : 'none';
    });
    document.getElementById('mode').dispatchEvent(new Event('change'));
</script>
@endsection
