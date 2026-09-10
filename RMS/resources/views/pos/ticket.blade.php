<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Ticket POS - Don Pulpo</title>
<style>
    @page { margin: 0; }

    * { box-sizing: border-box; }

    html, body { margin: 0; padding: 0; background: #fff; }

    body {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 12px;
        color: #000;
    }

    .ticket { padding: 4mm; }

    /* HEADER */
    .header { text-align: center; }
    .logo { font-family: Arial, Helvetica, sans-serif; font-size: 26px; font-weight: 900; letter-spacing: -1px; margin-bottom: 2px; }
    .logo-img { width: 34mm; height: auto; margin-bottom: 4px; }
    .slogan { font-family: Arial, Helvetica, sans-serif; font-size: 10px; margin-bottom: 8px; }
    .business-info { font-size: 10px; line-height: 1.4; }

    /* GENERAL */
    .separator { border-top: 1px dashed #000; margin: 8px 0; font-size: 0; line-height: 0; }
    .center { text-align: center; }
    .bold { font-weight: bold; }
    table.kv { width: 100%; table-layout: fixed; border-collapse: collapse; }
    table.kv col.k { width: 40%; }
    table.kv col.v { width: 60%; }
    table.kv td { padding: 1px 0; word-wrap: break-word; }
    table.kv td.v { text-align: right; }

    /* ORDER INFO */
    .order-title { text-align: center; font-weight: bold; font-size: 14px; margin-bottom: 6px; }
    .order-info { font-size: 11px; }

    /* PRODUCTS */
    table.products { width: 100%; table-layout: fixed; border-collapse: collapse; font-size: 11px; }
    table.products col.qty { width: 12%; }
    table.products col.desc { width: 38%; }
    table.products col.price { width: 25%; }
    table.products col.amount { width: 25%; }
    table.products th { font-size: 10px; text-align: left; padding-bottom: 4px; }
    table.products th.qty, table.products td.qty { text-align: center; }
    table.products th.price, table.products td.price,
    table.products th.amount, table.products td.amount { text-align: right; }
    table.products td { padding: 1px 0; vertical-align: top; }
    .product-name { font-weight: bold; font-size: 11px; line-height: 1.2; padding-top: 6px; }
    .modifiers { margin-left: 10%; font-size: 10px; line-height: 1.3; }
    .modifier::before { content: "+ "; }

    /* TOTALS */
    table.totals { width: 100%; table-layout: fixed; border-collapse: collapse; font-size: 11px; }
    table.totals col.k { width: 55%; }
    table.totals col.v { width: 45%; }
    table.totals td { padding: 2px 0; word-wrap: break-word; }
    table.totals td.v { text-align: right; }
    table.totals tr.grand-total td { font-size: 17px; font-weight: bold; padding-top: 4px; }

    /* PAYMENT */
    .payment-title { text-align: center; font-weight: bold; margin-bottom: 5px; }

    /* CUSTOMER */
    .customer { font-size: 10px; line-height: 1.4; }

    /* QR */
    .qr { text-align: center; margin: 12px 0 5px; }
    .qr-box { width: 90px; height: 90px; border: 2px solid #000; margin: 0 auto; text-align: center; font-size: 9px; padding-top: 32px; }

    /* FOOTER */
    .footer { text-align: center; font-size: 10px; line-height: 1.5; }
    .footer-message { font-family: Arial, Helvetica, sans-serif; font-size: 12px; font-weight: bold; margin-bottom: 5px; }
</style>
</head>
<body>
<div class="ticket">

    <header class="header">
        @if($logoDataUri)
        <img src="{{ $logoDataUri }}" alt="Don Pulpo" class="logo-img">
        @else
        <div class="logo">DON PULPO</div>
        @endif
        <div class="slogan">Desde el mar hasta tu paladar</div>
        {{--
            Datos de contacto/fiscales de ejemplo — pendientes de reemplazar por los
            datos reales del negocio antes de imprimir tickets en producción.
        <div class="business-info">
            Restaurante Don Pulpo<br>
            RFC: DPU000000XXX<br>
            Av. Ejemplo #123, Guadalupe, N.L.<br>
            Tel. 81 1234 5678
        </div>
        --}}
    </header>

    <div class="separator"></div>

    <div class="order-title">NOTA DE VENTA</div>

    <table class="kv order-info">
        <colgroup><col class="k"><col class="v"></colgroup>
        <tr><td>Ticket:</td><td class="v bold">#{{ str_pad($order->id, 8, '0', STR_PAD_LEFT) }}</td></tr>
        <tr><td>Fecha:</td><td class="v">{{ ($order->paid_at ?? now())->format('d/m/Y') }}</td></tr>
        <tr><td>Hora:</td><td class="v">{{ ($order->paid_at ?? now())->format('H:i:s') }}</td></tr>
        <tr><td>Orden:</td><td class="v">{{ $order->order_number }}</td></tr>
        <tr><td>Mesa:</td><td class="v">{{ $order->table_name ?: $orderTypeLabel }}</td></tr>
        <tr><td>Mesero:</td><td class="v">{{ $order->user->name ?? '—' }}</td></tr>
    </table>

    <div class="separator"></div>

    <table class="products">
        <colgroup>
            <col class="qty"><col class="desc"><col class="price"><col class="amount">
        </colgroup>
        <thead>
            <tr>
                <th class="qty">CANT</th>
                <th>DESCRIPCIÓN</th>
                <th class="price">PRECIO</th>
                <th class="amount">IMPORTE</th>
            </tr>
        </thead>
        <tbody>
        @foreach($order->items as $item)
            <tr>
                <td colspan="4" class="product-name">{{ $item->name_snapshot }}</td>
            </tr>
            <tr>
                <td class="qty">{{ $item->quantity }}</td>
                <td></td>
                <td class="price">${{ number_format($item->unit_price, 2) }}</td>
                <td class="amount">${{ number_format($item->line_total, 2) }}</td>
            </tr>
            @if($item->notes)
            <tr>
                <td></td>
                <td colspan="3">
                    <div class="modifiers">
                        @foreach(explode(',', $item->notes) as $mod)
                            @if(trim($mod) !== '')
                            <div class="modifier">{{ trim($mod) }}</div>
                            @endif
                        @endforeach
                    </div>
                </td>
            </tr>
            @endif
        @endforeach
        </tbody>
    </table>

    <div class="separator"></div>

    <table class="totals">
        <colgroup><col class="k"><col class="v"></colgroup>
        <tr><td>Subtotal</td><td class="v">${{ number_format($order->subtotal, 2) }}</td></tr>
        @if($order->discount_amount > 0)
        <tr><td>Descuento</td><td class="v">-${{ number_format($order->discount_amount, 2) }}</td></tr>
        <tr><td>Subtotal neto</td><td class="v">${{ number_format($order->subtotal - $order->discount_amount, 2) }}</td></tr>
        @endif
        @if($order->tax > 0)
        <tr><td>IVA</td><td class="v">${{ number_format($order->tax, 2) }}</td></tr>
        @endif
    </table>

    <div class="separator"></div>

    <table class="totals">
        <colgroup><col class="k"><col class="v"></colgroup>
        <tr class="grand-total"><td>TOTAL</td><td class="v">${{ number_format($totalBeforeTip, 2) }}</td></tr>
    </table>

    <div class="separator"></div>

    <div class="payment-title">FORMA DE PAGO</div>
    <table class="totals">
        <colgroup><col class="k"><col class="v"></colgroup>
        @foreach($order->payments as $payment)
        <tr><td>{{ $paymentMethodLabels[$payment->method] ?? $payment->method }}</td><td class="v">${{ number_format($payment->amount, 2) }}</td></tr>
        @endforeach
        @if($change > 0)
        <tr><td>Cambio</td><td class="v">${{ number_format($change, 2) }}</td></tr>
        @endif
    </table>

    @if($order->tip > 0)
    <div class="separator"></div>
    <table class="totals">
        <colgroup><col class="k"><col class="v"></colgroup>
        <tr><td>Propina</td><td class="v">${{ number_format($order->tip, 2) }}</td></tr>
        <tr class="bold"><td>Total con propina</td><td class="v">${{ number_format($order->total, 2) }}</td></tr>
    </table>
    @endif

    <div class="separator"></div>

    <div class="customer">
        <strong>CLIENTE</strong><br>
        {{ $order->customer_name ?: 'Público en general' }}
        {{--
            Régimen fiscal de ejemplo — el sistema no captura este dato hoy, no imprimir
            uno genérico en producción hasta tener el régimen real del cliente.
        <br>Régimen fiscal: 616 - Sin obligaciones fiscales
        --}}
    </div>

    {{--
        Bloque de autofacturación (folio + QR) — no hay sistema de facturación
        conectado todavía. No mostrar esta invitación hasta que exista de verdad,
        para no prometerle al cliente algo que no se le puede dar.
    <div class="separator"></div>

    <div class="center footer">
        ¿Necesitas factura?<br>
        Solicítala con tu ticket de compra.
        <br><br>
        <strong>FOLIO PARA FACTURAR</strong><br>
        {{ str_pad($order->id, 8, '0', STR_PAD_LEFT) }}
    </div>

    <div class="qr">
        <div class="qr-box">QR<br>FACTURACIÓN</div>
    </div>
    --}}

    <div class="separator"></div>

    <div class="footer">
        <div class="footer-message">¡GRACIAS POR TU VISITA!</div>
        Esperamos verte pronto.
        <br>
        Don Pulpo
        {{--
            Redes sociales de ejemplo — confirmar el usuario real antes de imprimirlo
            en producción.
        <br><br>
        Síguenos en redes sociales<br>
        @donpulpo
        --}}
        <br><br>
        Ticket generado por<br>
        DON PULPO POS
    </div>

</div>
</body>
</html>
