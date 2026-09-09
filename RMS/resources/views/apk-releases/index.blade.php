@extends('layouts.app')
@section('title', 'APK del POS — Don Pulpo RMS')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 mt-2">
    <h2 class="h4 mb-0">APK del POS</h2>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card shadow-sm border-0 mb-4" style="max-width:560px">
    <div class="card-header">Subir nueva versión</div>
    <div class="card-body">
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <form action="{{ route('apk-releases.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-semibold">Versión</label>
                <input type="text" name="version" class="form-control" value="{{ old('version') }}"
                       placeholder="Ej: 1.0.0" required maxlength="50" />
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Notas (opcional)</label>
                <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
            </div>
            <div class="mb-4">
                <label class="form-label fw-semibold">Archivo APK</label>
                <input type="file" name="apk" class="form-control" accept=".apk" required />
            </div>
            <button type="submit" class="btn btn-dp w-100">Subir APK</button>
        </form>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Versión</th>
                    <th>Archivo</th>
                    <th>Tamaño</th>
                    <th>Notas</th>
                    <th>Subido por</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($releases as $release)
                <tr>
                    <td>{{ $release->id }}</td>
                    <td><code>{{ $release->version }}</code></td>
                    <td>{{ $release->original_name }}</td>
                    <td>{{ $release->formattedSize() }}</td>
                    <td>{{ $release->notes }}</td>
                    <td>{{ $release->uploader?->name ?? '—' }}</td>
                    <td>{{ $release->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        <a href="{{ route('apk-releases.download', $release) }}" class="btn btn-sm btn-dp-outline">Descargar</a>
                        <form action="{{ route('apk-releases.destroy', $release) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('¿Eliminar esta versión del APK?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Eliminar</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted py-4">No hay versiones de APK subidas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $releases->links() }}</div>
@endsection
