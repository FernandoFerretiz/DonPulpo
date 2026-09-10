@extends('layouts.app')
@section('title', 'Editar mesa — Don Pulpo RMS')
@section('content')
<div class="d-flex align-items-center gap-2 mb-3 mt-2">
    <a href="{{ route('tables.index') }}" class="btn btn-sm btn-outline-secondary">← Volver</a>
    <h2 class="h4 mb-0">Editar mesa</h2>
</div>

<div class="card shadow-sm border-0" style="max-width:480px">
    <div class="card-body">
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <form action="{{ route('tables.update', $table) }}" method="POST">
            @csrf @method('PUT')
            @include('tables._form')
            <button type="submit" class="btn btn-dp w-100">Guardar cambios</button>
        </form>
    </div>
</div>
@endsection
