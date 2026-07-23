@extends('layouts.app')
@section('title', 'Códigos de descuento — Don Pulpo RMS')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 mt-2">
    <h2 class="h4 mb-0">Códigos de descuento</h2>
    <a href="{{ route('discount-codes.create') }}" class="btn btn-dp">+ Nuevo código</a>
</div>

<form method="GET" action="{{ route('discount-codes.index') }}" class="mb-3">
    <div class="input-group" style="max-width:380px;">
        <input type="search" name="search" class="form-control" placeholder="Buscar por código o beneficiario…"
               value="{{ $search ?? '' }}" autocomplete="off">
        <button class="btn btn-outline-secondary" type="submit">Buscar</button>
        @if($search)
            <a href="{{ route('discount-codes.index') }}" class="btn btn-outline-secondary">✕</a>
        @endif
    </div>
</form>

<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Código</th>
                    <th>Porcentaje</th>
                    <th>Beneficiario</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($codes as $dc)
                <tr>
                    <td>{{ $dc->id }}</td>
                    <td><code>{{ $dc->code }}</code></td>
                    <td>{{ rtrim(rtrim(number_format($dc->percentage, 2), '0'), '.') }}%</td>
                    <td>{{ $dc->beneficiary_name }}</td>
                    <td>
                        <span class="badge {{ $dc->status === 'active' ? 'badge-active' : 'badge-inactive' }}">
                            {{ $dc->getStatusLabel() }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('discount-codes.edit', $dc) }}" class="btn btn-sm btn-outline-secondary">Editar</a>
                        <form action="{{ route('discount-codes.destroy', $dc) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('¿Eliminar este código de descuento?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Eliminar</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-4">No hay códigos de descuento registrados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $codes->links() }}</div>
@endsection
