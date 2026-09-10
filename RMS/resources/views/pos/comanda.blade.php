<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Comanda - {{ $order->order_number }}</title>
<style>
    @page { margin: 0; }
    * { box-sizing: border-box; }
    html, body { margin: 0; padding: 0; background: #fff; }
    body { font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #000; }

    .comanda { padding: 4mm; }

    .header { text-align: center; }
    .title { font-family: Arial, Helvetica, sans-serif; font-size: 20px; font-weight: 700; }
    .subtitle { font-size: 12px; margin-top: 2px; }

    .separator { border-top: 2px dashed #000; margin: 8px 0; font-size: 0; line-height: 0; }

    table.kv { width: 100%; table-layout: fixed; border-collapse: collapse; font-size: 13px; }
    table.kv col.k { width: 38%; }
    table.kv col.v { width: 62%; }
    table.kv td { padding: 2px 0; word-wrap: break-word; }
    table.kv td.v { text-align: right; font-weight: bold; }

    .items { margin-top: 4px; }
    .item { padding: 8px 0; border-bottom: 1px dashed #999; }
    .item-name { font-size: 17px; font-weight: 700; }
    .item-qty { display: inline-block; min-width: 26px; }
    .item-notes { margin-top: 4px; font-size: 13px; font-weight: bold; }
    .item-notes .flag { text-decoration: underline; }
    .item-addition { display: inline-block; margin-left: 6px; font-size: 11px; font-weight: bold; border: 1px solid #000; padding: 1px 5px; }
    .reprint-flag { text-align: center; font-size: 11px; font-weight: bold; border: 1px dashed #000; padding: 4px; margin-bottom: 8px; }
    .empty { text-align: center; font-size: 14px; font-weight: bold; padding: 20px 0; }

    .footer { text-align: center; font-size: 11px; margin-top: 10px; }
</style>
</head>
<body>
<div class="comanda">

    <div class="header">
        <div class="title">COMANDA DE COCINA</div>
        <div class="subtitle">No es un comprobante de pago</div>
    </div>

    <div class="separator"></div>

    <table class="kv">
        <colgroup><col class="k"><col class="v"></colgroup>
        <tr><td>Orden:</td><td class="v">{{ $order->order_number }}</td></tr>
        <tr><td>Fecha:</td><td class="v">{{ now()->format('d/m/Y') }}</td></tr>
        <tr><td>Hora:</td><td class="v">{{ now()->format('H:i:s') }}</td></tr>
        <tr><td>Tipo:</td><td class="v">{{ $orderTypeLabel }}</td></tr>
        <tr><td>Mesa:</td><td class="v">{{ $order->table_name ?: '—' }}</td></tr>
        @if($order->customer_name)
        <tr><td>Cliente:</td><td class="v">{{ $order->customer_name }}</td></tr>
        @endif
        <tr><td>Mesero:</td><td class="v">{{ $order->user->name ?? '—' }}</td></tr>
    </table>

    <div class="separator"></div>

    @if($isReprint)
    <div class="reprint-flag">⚠ REIMPRESIÓN COMPLETA — puede tener platillos ya preparados</div>
    @endif

    @if(count($lines) === 0)
    <div class="empty">No hay platillos nuevos para enviar a cocina.</div>
    @else
    <div class="items">
        @foreach($lines as $line)
        <div class="item">
            <span class="item-qty">{{ $line['quantity'] }}x</span>
            <span class="item-name">{{ $line['name'] }}</span>
            @if($line['is_addition'])
            <span class="item-addition">AGREGADO</span>
            @endif
            @if($line['notes'])
            <div class="item-notes"><span class="flag">OJO:</span> {{ $line['notes'] }}</div>
            @endif
        </div>
        @endforeach
    </div>
    @endif

    @if($order->notes)
    <div class="separator"></div>
    <div class="item-notes">Nota general: {{ $order->notes }}</div>
    @endif

    <div class="separator"></div>

    <div class="footer">— Fin de comanda —</div>

</div>
</body>
</html>
