@extends('layouts.app')

@section('title', 'Corte #'.$shift->id.' — Don Pulpo RMS')

@php
    use App\Models\CashMovement;

    $cashSales      = $shift->cashSales();
    $cardSales      = $shift->cardSales();
    $transferSales  = $shift->transferSales();
    $manualMovs     = $shift->manualMovements();
    $pettyCashMovs  = $shift->pettyCashMovements();
    $manualIncome   = $manualMovs->filter(fn ($m) => $m->isIncome())->sum('amount');
    $manualExpense  = $manualMovs->filter(fn ($m) => $m->isExpense())->sum('amount');
    $pettyCashTotal = $pettyCashMovs->sum('amount');

    $cardDiff     = $shift->cardDifference();
    $transferDiff = $shift->transferDifference();

    $discountedOrders = $shift->discountedOrders();
    $discountTotal     = $discountedOrders->sum('discount_amount');

    $badge = match($shift->status) {
        'open'      => 'info',
        'closed'    => 'success',
        'cancelled' => 'secondary',
        default     => 'light',
    };
@endphp

@section('content')
<div class="d-flex justify-content-between align-items-center mt-4 mb-3">
    <div>
        <h4 class="fw-bold mb-0">Corte #{{ $shift->id }}</h4>
        <span class="text-muted" style="font-size:.9rem">
            {{ $shift->user?->name ?? '—' }} · Terminal {{ $shift->terminal_id ?? '—' }}
        </span>
    </div>
    <div class="d-flex align-items-center gap-2">
        <span class="badge bg-{{ $badge }} fs-6">{{ $shift->getStatusLabel() }}</span>
        <a href="{{ route('shifts.index') }}" class="btn btn-dp-outline btn-sm">&larr; Volver</a>
    </div>
</div>

{{-- Datos generales --}}
<div class="card shadow-sm mb-3">
    <div class="card-body">
        <div class="row text-center">
            <div class="col-6 col-md-3 mb-2">
                <div class="text-muted" style="font-size:.78rem">Apertura</div>
                <div class="fw-semibold">{{ $shift->opened_at?->format('d/m/Y H:i') ?? '—' }}</div>
            </div>
            <div class="col-6 col-md-3 mb-2">
                <div class="text-muted" style="font-size:.78rem">Cierre</div>
                <div class="fw-semibold">{{ $shift->closed_at?->format('d/m/Y H:i') ?? '—' }}</div>
            </div>
            <div class="col-6 col-md-3 mb-2">
                <div class="text-muted" style="font-size:.78rem">Fondo inicial</div>
                <div class="fw-semibold">${{ number_format($shift->opening_cash, 2) }}</div>
            </div>
            <div class="col-6 col-md-3 mb-2">
                <div class="text-muted" style="font-size:.78rem">Notas</div>
                <div class="fw-semibold">{{ $shift->notes ?? '—' }}</div>
            </div>
        </div>
    </div>
</div>

{{-- Resumen de caja: efectivo, tarjeta, transferencia, diferencias --}}
<div class="row mb-3">
    <div class="col-md-4 mb-3 mb-md-0">
        <div class="card shadow-sm h-100">
            <div class="card-header">Efectivo</div>
            <div class="card-body" style="font-size:.9rem">
                <div class="d-flex justify-content-between"><span>Ventas efectivo</span><span>${{ number_format($cashSales, 2) }}</span></div>
                <div class="d-flex justify-content-between"><span>Fondo inicial</span><span>${{ number_format($shift->opening_cash, 2) }}</span></div>
                <div class="d-flex justify-content-between"><span>Ingresos manuales</span><span>${{ number_format($manualIncome, 2) }}</span></div>
                <div class="d-flex justify-content-between text-danger"><span>Retiros / devoluciones / vales</span><span>-${{ number_format($manualExpense + $pettyCashTotal, 2) }}</span></div>
                <hr class="my-2">
                <div class="d-flex justify-content-between fw-bold"><span>Esperado</span><span>{{ !is_null($shift->expected_cash) ? '$'.number_format($shift->expected_cash, 2) : '—' }}</span></div>
                <div class="d-flex justify-content-between fw-bold"><span>Contado</span><span>{{ !is_null($shift->counted_cash) ? '$'.number_format($shift->counted_cash, 2) : '—' }}</span></div>
                <div class="d-flex justify-content-between fw-bold {{ $shift->difference < 0 ? 'text-danger' : ($shift->difference > 0 ? 'text-success' : '') }}">
                    <span>Diferencia</span><span>{{ !is_null($shift->difference) ? '$'.number_format($shift->difference, 2) : '—' }}</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3 mb-md-0">
        <div class="card shadow-sm h-100">
            <div class="card-header">Tarjeta</div>
            <div class="card-body" style="font-size:.9rem">
                <div class="d-flex justify-content-between"><span>Ventas tarjeta</span><span>${{ number_format($cardSales, 2) }}</span></div>
                <hr class="my-2">
                <div class="d-flex justify-content-between fw-bold"><span>Esperado</span><span>${{ number_format($cardSales, 2) }}</span></div>
                <div class="d-flex justify-content-between fw-bold"><span>Contado</span><span>{{ !is_null($shift->counted_card) ? '$'.number_format($shift->counted_card, 2) : '—' }}</span></div>
                <div class="d-flex justify-content-between fw-bold {{ $cardDiff < 0 ? 'text-danger' : ($cardDiff > 0 ? 'text-success' : '') }}">
                    <span>Diferencia</span><span>{{ !is_null($cardDiff) ? '$'.number_format($cardDiff, 2) : '—' }}</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm h-100">
            <div class="card-header">Transferencia</div>
            <div class="card-body" style="font-size:.9rem">
                <div class="d-flex justify-content-between"><span>Ventas transferencia</span><span>${{ number_format($transferSales, 2) }}</span></div>
                <hr class="my-2">
                <div class="d-flex justify-content-between fw-bold"><span>Esperado</span><span>${{ number_format($transferSales, 2) }}</span></div>
                <div class="d-flex justify-content-between fw-bold"><span>Contado</span><span>{{ !is_null($shift->counted_transfer) ? '$'.number_format($shift->counted_transfer, 2) : '—' }}</span></div>
                <div class="d-flex justify-content-between fw-bold {{ $transferDiff < 0 ? 'text-danger' : ($transferDiff > 0 ? 'text-success' : '') }}">
                    <span>Diferencia</span><span>{{ !is_null($transferDiff) ? '$'.number_format($transferDiff, 2) : '—' }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Movimientos manuales --}}
<div class="card shadow-sm mb-3">
    <div class="card-header">Movimientos manuales</div>
    <div class="card-body p-0">
        @if($manualMovs->isEmpty())
            <p class="text-center text-muted py-4 mb-0">No hay movimientos manuales en este corte.</p>
        @else
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle" style="font-size:.9rem">
                <thead>
                    <tr>
                        <th>Tipo</th>
                        <th>Descripción</th>
                        <th>Usuario</th>
                        <th>Fecha</th>
                        <th class="text-end">Monto</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($manualMovs as $m)
                    <tr>
                        <td>{{ $m->getTypeLabel() }}</td>
                        <td>{{ $m->description ?? '—' }}</td>
                        <td>{{ $m->user?->name ?? '—' }}</td>
                        <td>{{ $m->created_at->format('d/m/y H:i') }}</td>
                        <td class="text-end fw-bold {{ $m->isExpense() ? 'text-danger' : 'text-success' }}">
                            {{ $m->isExpense() ? '-' : '+' }}${{ number_format($m->amount, 2) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>

{{-- Vales de caja chica --}}
<div class="card shadow-sm mb-3">
    <div class="card-header">Vales de caja chica</div>
    <div class="card-body p-0">
        @if($shift->pettyCashVouchers->isEmpty())
            <p class="text-center text-muted py-4 mb-0">No hay vales de caja chica en este corte.</p>
        @else
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle" style="font-size:.9rem">
                <thead>
                    <tr>
                        <th>Folio</th>
                        <th>Concepto</th>
                        <th>Categoría</th>
                        <th>Beneficiario</th>
                        <th>Solicitado por</th>
                        <th>Estado</th>
                        <th class="text-end">Monto</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($shift->pettyCashVouchers as $v)
                    <tr>
                        <td><code>{{ $v->folio }}</code></td>
                        <td style="max-width:220px;white-space:normal">{{ Str::limit($v->concept, 60) }}</td>
                        <td>{{ $v->category?->name ?? '—' }}</td>
                        <td>{{ $v->beneficiary ?? '—' }}</td>
                        <td>{{ $v->requestedBy?->name ?? '—' }}</td>
                        <td>
                            @php
                                $vBadge = match($v->status) {
                                    'pending'    => 'warning',
                                    'authorized' => 'info',
                                    'paid'       => 'success',
                                    'rejected'   => 'danger',
                                    'cancelled'  => 'secondary',
                                    default      => 'light',
                                };
                            @endphp
                            <span class="badge bg-{{ $vBadge }}">{{ $v->getStatusLabel() }}</span>
                        </td>
                        <td class="text-end fw-bold">${{ number_format($v->amount, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="6" class="text-end fw-bold">Total</td>
                        <td class="text-end fw-bold">${{ number_format($pettyCashTotal, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @endif
    </div>
</div>

{{-- Descuentos --}}
<div class="card shadow-sm mb-3">
    <div class="card-header">Descuentos</div>
    <div class="card-body p-0">
        @if($discountedOrders->isEmpty())
            <p class="text-center text-muted py-4 mb-0">No se aplicaron descuentos en este corte.</p>
        @else
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle" style="font-size:.9rem">
                <thead>
                    <tr>
                        <th>Orden</th>
                        <th>Código</th>
                        <th class="text-end">% Descuento</th>
                        <th class="text-end">Saldo aplicado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($discountedOrders as $order)
                    <tr>
                        <td>{{ $order->order_number }}</td>
                        <td><code>{{ $order->discount_code }}</code></td>
                        <td class="text-end">{{ number_format($order->discount_percent, 2) }}%</td>
                        <td class="text-end fw-bold">${{ number_format($order->discount_amount, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-end fw-bold">Total</td>
                        <td class="text-end fw-bold">${{ number_format($discountTotal, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @endif
    </div>
</div>
@endsection
