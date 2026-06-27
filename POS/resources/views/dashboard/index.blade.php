@extends('layouts.app')
@section('title', 'Dashboard — Don Pulpo POS')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h4 mb-0">📊 Dashboard del día</h2>
    <a href="{{ route('pos') }}" class="btn btn-dp">Ir al POS →</a>
</div>

<div class="row g-4" id="dashboardCards">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-4" style="border-radius:20px">
            <div style="font-size:40px">💰</div>
            <p class="text-muted small fw-bold mt-2 mb-1">Ventas del día</p>
            <h3 class="h2 fw-bold" id="salesDay">—</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-4" style="border-radius:20px">
            <div style="font-size:40px">🟡</div>
            <p class="text-muted small fw-bold mt-2 mb-1">Órdenes abiertas</p>
            <h3 class="h2 fw-bold" id="openOrders">—</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-4" style="border-radius:20px">
            <div style="font-size:40px">✅</div>
            <p class="text-muted small fw-bold mt-2 mb-1">Órdenes pagadas</p>
            <h3 class="h2 fw-bold" id="paidOrders">—</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-4" style="border-radius:20px">
            <div style="font-size:40px">🎟️</div>
            <p class="text-muted small fw-bold mt-2 mb-1">Ticket promedio</p>
            <h3 class="h2 fw-bold" id="avgTicket">—</h3>
        </div>
    </div>
</div>

<div class="mt-3 text-muted small text-end" id="dashDate"></div>
@endsection

@section('scripts')
<script>
const money = new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' });

async function loadDashboard() {
    try {
        const res = await fetch('/api/v1/dashboard/summary');
        const json = await res.json();
        if (!json.success) return;
        const d = json.data;
        document.getElementById('salesDay').textContent   = money.format(d.sales_today);
        document.getElementById('openOrders').textContent  = d.open_orders;
        document.getElementById('paidOrders').textContent  = d.paid_orders;
        document.getElementById('avgTicket').textContent   = money.format(d.average_ticket);
        document.getElementById('dashDate').textContent    = 'Fecha: ' + d.date;
    } catch(e) {
        console.error('Error cargando dashboard', e);
    }
}

loadDashboard();
setInterval(loadDashboard, 30000);
</script>
@endsection
