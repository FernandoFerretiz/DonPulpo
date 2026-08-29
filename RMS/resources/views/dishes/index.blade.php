@extends('layouts.app')
@section('title', 'Platillos — Don Pulpo RMS')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 mt-2">
    <h2 class="h4 mb-0">Platillos</h2>
    <a href="{{ route('dishes.create') }}" class="btn btn-dp">+ Nuevo platillo</a>
</div>

<div class="mb-3">
    <div class="input-group" style="max-width:380px;">
        <input type="search" id="dishSearchInput" class="form-control" placeholder="Buscar platillo…"
               value="{{ $search ?? '' }}" autocomplete="off">
        <button class="btn btn-outline-secondary" type="button" id="dishSearchClear">✕</button>
    </div>
</div>

<div id="dishTableContainer">
    @include('dishes._table')
</div>

{{-- ══════════════════════════════════════════
     MODAL: Editar platillo
═══════════════════════════════════════════ --}}
<div class="modal fade" id="dishEditModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" style="max-width:640px">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar platillo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="dishEditModalBody">
                <div class="text-center text-muted py-4">Cargando…</div>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    const searchInput  = document.getElementById('dishSearchInput');
    const clearBtn     = document.getElementById('dishSearchClear');
    const tableWrap    = document.getElementById('dishTableContainer');
    const modalEl      = document.getElementById('dishEditModal');
    const modalBody    = document.getElementById('dishEditModalBody');
    const indexUrl     = @json(route('dishes.index'));
    // bootstrap.bundle.min.js se carga al final del layout, después de este bloque,
    // así que la instancia del modal se crea al primer uso (no al cargar la página).
    function getModal() { return bootstrap.Modal.getOrCreateInstance(modalEl); }
    let debounceTimer  = null;

    async function runSearch() {
        const q   = searchInput.value.trim();
        const url = indexUrl + '?partial=1' + (q ? '&search=' + encodeURIComponent(q) : '');
        const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        tableWrap.innerHTML = await res.text();
    }

    searchInput.addEventListener('input', () => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(runSearch, 300);
    });

    clearBtn.addEventListener('click', () => {
        searchInput.value = '';
        runSearch();
    });

    // Delegación de eventos: la tabla se reemplaza en cada búsqueda, así que los
    // listeners se enganchan al contenedor fijo, no a los botones/links dentro de ella.
    tableWrap.addEventListener('click', async e => {
        const editBtn = e.target.closest('.js-edit-dish');
        if (!editBtn) return;
        await openEditModal(editBtn.dataset.id);
    });

    // Paginación: interceptar los links del paginador para que también
    // carguen sin recargar la página completa.
    tableWrap.addEventListener('click', async e => {
        const link = e.target.closest('.pagination a');
        if (!link) return;
        e.preventDefault();
        const res = await fetch(link.href + (link.href.includes('?') ? '&' : '?') + 'partial=1', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        });
        tableWrap.innerHTML = await res.text();
    });

    async function openEditModal(id) {
        modalBody.innerHTML = '<div class="text-center text-muted py-4">Cargando…</div>';
        getModal().show();
        try {
            const res = await fetch(`/dishes/${id}/edit?modal=1`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            modalBody.innerHTML = await res.text();
            bindEditForm();
        } catch {
            modalBody.innerHTML = '<div class="alert alert-danger mb-0">No se pudo cargar el platillo.</div>';
        }
    }

    function bindEditForm() {
        const form = document.getElementById('dishEditForm');
        if (!form) return;
        form.addEventListener('submit', async e => {
            e.preventDefault();
            const btn = form.querySelector('button[type=submit]');
            const originalText = btn.textContent;
            btn.disabled = true; btn.textContent = 'Guardando...';

            try {
                const res = await fetch(form.action, {
                    method: 'POST', // el @method('PUT') en el form hace el spoofing
                    body: new FormData(form),
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });
                const json = await res.json();

                if (res.ok && json.success) {
                    getModal().hide();
                    await runSearch();
                } else if (res.status === 422) {
                    showFormErrors(json.errors || {});
                } else {
                    showFormErrors({}, json.message || 'Ocurrió un error al guardar.');
                }
            } catch {
                showFormErrors({}, 'Error de conexión.');
            } finally {
                btn.disabled = false; btn.textContent = originalText;
            }
        });
    }

    function showFormErrors(errors, genericMessage) {
        const box = document.getElementById('dishFormErrors');
        if (!box) return;
        const messages = genericMessage ? [genericMessage] : Object.values(errors).flat();
        box.innerHTML = messages.length
            ? `<div class="alert alert-danger"><ul class="mb-0">${messages.map(m => `<li>${m}</li>`).join('')}</ul></div>`
            : '';
    }
})();
</script>
@endsection
