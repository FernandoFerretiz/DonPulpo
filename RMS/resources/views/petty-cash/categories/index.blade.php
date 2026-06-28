@extends('layouts.app')

@section('title', 'Categorías de Caja Chica — Don Pulpo RMS')

@section('content')
<div class="d-flex justify-content-between align-items-center mt-4 mb-3">
    <h4 class="fw-bold mb-0">Categorías de Caja Chica</h4>
    <a href="{{ route('petty-cash.categories.create') }}" class="btn btn-dp btn-sm px-3">+ Nueva categoría</a>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        @if($categories->isEmpty())
            <p class="text-center text-muted py-5">No hay categorías. Crea una para empezar.</p>
        @else
        <table class="table table-hover mb-0 align-middle" style="font-size:.9rem">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th class="text-center">Vales</th>
                    <th class="text-center">Estado</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($categories as $cat)
                <tr>
                    <td>{{ $cat->id }}</td>
                    <td class="fw-semibold">{{ $cat->name }}</td>
                    <td class="text-center">{{ $cat->vouchers_count }}</td>
                    <td class="text-center">
                        @if($cat->is_active)
                            <span class="badge badge-active">Activa</span>
                        @else
                            <span class="badge badge-inactive">Inactiva</span>
                        @endif
                    </td>
                    <td class="text-center" style="white-space:nowrap">
                        <a href="{{ route('petty-cash.categories.edit', $cat) }}" class="btn btn-dp-outline btn-sm py-0 px-2">Editar</a>
                        @if($cat->vouchers_count == 0)
                        <form method="POST" action="{{ route('petty-cash.categories.destroy', $cat) }}" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm py-0 px-2"
                                    onclick="return confirm('¿Eliminar categoría {{ addslashes($cat->name) }}?')">
                                Eliminar
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
    @if($categories->hasPages())
    <div class="card-footer d-flex justify-content-center py-2">
        {{ $categories->links() }}
    </div>
    @endif
</div>
@endsection
