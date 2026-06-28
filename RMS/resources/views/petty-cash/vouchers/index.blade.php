@extends('layouts.app')

@section('title', 'Vales de Caja Chica — Don Pulpo RMS')

@section('content')
<div class="d-flex justify-content-between align-items-center mt-4 mb-3">
    <h4 class="fw-bold mb-0">Vales de Caja Chica</h4>
    <a href="{{ route('petty-cash.vouchers.create') }}" class="btn btn-dp btn-sm px-3">+ Nuevo vale</a>
</div>

{{-- Tabs de estado --}}
<ul class="nav nav-tabs mb-3" style="border-bottom-color:var(--blue-muted)">
    @foreach($statusTabs as $key => $label)
        <li class="nav-item">
            <a class="nav-link {{ $status == $key ? 'active' : '' }}"
               href="{{ route('petty-cash.vouchers.index', $key ? ['status' => $key] : []) }}"
               style="{{ $status == $key ? 'border-color:var(--gold) var(--gold) #fff;color:var(--gold-dark);font-weight:700' : 'color:#4F6C81' }}">
                {{ $label }}
            </a>
        </li>
    @endforeach
</ul>

{{-- Reject modal (single, reused per row) --}}
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form id="rejectForm" method="POST" class="modal-content">
            @csrf @method('PATCH')
            <div class="modal-header" style="background:var(--navy-deep)">
                <h5 class="modal-title text-white">Rechazar vale</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <label class="form-label fw-semibold">Motivo de rechazo</label>
                <textarea name="rejection_reason" class="form-control" rows="3" required placeholder="Indica el motivo..."></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-danger btn-sm">Rechazar</button>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        @if($vouchers->isEmpty())
            <p class="text-center text-muted py-5">No hay vales con este filtro.</p>
        @else
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle" style="font-size:.9rem">
                <thead>
                    <tr>
                        <th>Folio</th>
                        <th>Concepto</th>
                        <th>Categoría</th>
                        <th>Beneficiario</th>
                        <th class="text-end">Monto</th>
                        <th>Estado</th>
                        <th>Solicitado por</th>
                        <th>Fecha</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($vouchers as $v)
                    <tr>
                        <td><code>{{ $v->folio }}</code></td>
                        <td style="max-width:200px;white-space:normal">{{ Str::limit($v->concept, 60) }}</td>
                        <td>{{ $v->category?->name ?? '—' }}</td>
                        <td>{{ $v->beneficiary ?? '—' }}</td>
                        <td class="text-end fw-bold">${{ number_format($v->amount, 2) }}</td>
                        <td>
                            @php
                                $badge = match($v->status) {
                                    'pending'    => 'warning',
                                    'authorized' => 'info',
                                    'paid'       => 'success',
                                    'rejected'   => 'danger',
                                    'cancelled'  => 'secondary',
                                    default      => 'light',
                                };
                            @endphp
                            <span class="badge bg-{{ $badge }}">{{ $v->getStatusLabel() }}</span>
                        </td>
                        <td>{{ $v->requestedBy?->name ?? '—' }}</td>
                        <td>{{ $v->created_at->format('d/m/y H:i') }}</td>
                        <td class="text-center" style="white-space:nowrap">
                            @if($v->isPending())
                                <form method="POST" action="{{ route('petty-cash.vouchers.authorize', $v) }}" class="d-inline">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn-success btn-sm py-0 px-2"
                                            onclick="return confirm('¿Autorizar vale {{ $v->folio }}?')">
                                        Autorizar
                                    </button>
                                </form>
                                <button class="btn btn-danger btn-sm py-0 px-2"
                                        data-bs-toggle="modal" data-bs-target="#rejectModal"
                                        data-action="{{ route('petty-cash.vouchers.reject', $v) }}">
                                    Rechazar
                                </button>
                            @endif
                            @if(!$v->isPaid() && $v->status !== 'cancelled')
                                <form method="POST" action="{{ route('petty-cash.vouchers.cancel', $v) }}" class="d-inline">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn-outline-secondary btn-sm py-0 px-2"
                                            onclick="return confirm('¿Cancelar vale {{ $v->folio }}?')">
                                        Cancelar
                                    </button>
                                </form>
                            @endif
                            @if($v->authorizedBy)
                                <small class="text-muted d-block" style="font-size:.7rem">
                                    Auth: {{ $v->authorizedBy->name }}
                                </small>
                            @endif
                            @if($v->rejection_reason)
                                <small class="text-danger d-block" style="font-size:.7rem" title="{{ $v->rejection_reason }}">
                                    Motivo: {{ Str::limit($v->rejection_reason, 30) }}
                                </small>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
    @if($vouchers->hasPages())
    <div class="card-footer d-flex justify-content-center py-2">
        {{ $vouchers->links() }}
    </div>
    @endif
</div>

<script>
document.getElementById('rejectModal').addEventListener('show.bs.modal', function (e) {
    const btn = e.relatedTarget;
    document.getElementById('rejectForm').action = btn.dataset.action;
    document.querySelector('#rejectModal textarea').value = '';
});
</script>
@endsection
