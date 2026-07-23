@extends('layouts.app')

@section('title', 'Cortes de Caja — Don Pulpo RMS')

@section('content')
<div class="d-flex justify-content-between align-items-center mt-4 mb-3">
    <h4 class="fw-bold mb-0">Cortes de Caja</h4>
</div>

{{-- Tabs de estado --}}
<ul class="nav nav-tabs mb-3" style="border-bottom-color:var(--blue-muted)">
    @foreach($statusTabs as $key => $label)
        <li class="nav-item">
            <a class="nav-link {{ $status == $key ? 'active' : '' }}"
               href="{{ route('shifts.index', $key ? ['status' => $key] : []) }}"
               style="{{ $status == $key ? 'border-color:var(--gold) var(--gold) #fff;color:var(--gold-dark);font-weight:700' : 'color:#4F6C81' }}">
                {{ $label }}
            </a>
        </li>
    @endforeach
</ul>

<div class="card shadow-sm">
    <div class="card-body p-0">
        @if($shifts->isEmpty())
            <p class="text-center text-muted py-5">No hay cortes con este filtro.</p>
        @else
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle" style="font-size:.9rem">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Cajero</th>
                        <th>Terminal</th>
                        <th>Estado</th>
                        <th>Apertura</th>
                        <th>Cierre</th>
                        <th class="text-end">Fondo inicial</th>
                        <th class="text-end">Esperado</th>
                        <th class="text-end">Contado</th>
                        <th class="text-end">Diferencia</th>
                        <th class="text-center">Detalle</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($shifts as $s)
                    <tr>
                        <td>{{ $s->id }}</td>
                        <td>{{ $s->user?->name ?? '—' }}</td>
                        <td>{{ $s->terminal_id ?? '—' }}</td>
                        <td>
                            @php
                                $badge = match($s->status) {
                                    'open'      => 'info',
                                    'closed'    => 'success',
                                    'cancelled' => 'secondary',
                                    default     => 'light',
                                };
                            @endphp
                            <span class="badge bg-{{ $badge }}">{{ $s->getStatusLabel() }}</span>
                        </td>
                        <td>{{ $s->opened_at?->format('d/m/y H:i') ?? '—' }}</td>
                        <td>{{ $s->closed_at?->format('d/m/y H:i') ?? '—' }}</td>
                        <td class="text-end">${{ number_format($s->opening_cash, 2) }}</td>
                        <td class="text-end">{{ !is_null($s->expected_cash) ? '$'.number_format($s->expected_cash, 2) : '—' }}</td>
                        <td class="text-end">{{ !is_null($s->counted_cash) ? '$'.number_format($s->counted_cash, 2) : '—' }}</td>
                        <td class="text-end fw-bold {{ $s->difference < 0 ? 'text-danger' : ($s->difference > 0 ? 'text-success' : '') }}">
                            {{ !is_null($s->difference) ? '$'.number_format($s->difference, 2) : '—' }}
                        </td>
                        <td class="text-center">
                            <a href="{{ route('shifts.show', $s) }}" class="btn btn-dp-outline btn-sm py-0 px-2">Ver</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
    @if($shifts->hasPages())
    <div class="card-footer d-flex justify-content-center py-2">
        {{ $shifts->links() }}
    </div>
    @endif
</div>
@endsection
