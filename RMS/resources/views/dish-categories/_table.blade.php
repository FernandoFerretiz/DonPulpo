<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Slug</th>
                    <th>Orden</th>
                    <th>Platillos</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $cat)
                <tr>
                    <td>{{ $cat->id }}</td>
                    <td>{{ $cat->name }}</td>
                    <td><code>{{ $cat->slug }}</code></td>
                    <td>{{ $cat->display_order }}</td>
                    <td>{{ $cat->dishes_count }}</td>
                    <td>
                        <span class="badge {{ $cat->status === 'active' ? 'badge-active' : 'badge-inactive' }}">
                            {{ $cat->getStatusLabel() }}
                        </span>
                    </td>
                    <td>
                        <button type="button" class="btn btn-sm btn-outline-secondary js-edit-category" data-id="{{ $cat->id }}">Editar</button>
                        <form action="{{ route('dish-categories.destroy', $cat) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('¿Eliminar esta categoría?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Eliminar</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-4">No hay categorías registradas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $categories->appends(['search' => $search])->links() }}</div>
