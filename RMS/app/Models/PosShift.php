<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PosShift extends Model
{
    protected $fillable = [];  // read-only from RMS

    protected $casts = [
        'opening_cash'     => 'decimal:2',
        'expected_cash'    => 'decimal:2',
        'counted_cash'     => 'decimal:2',
        'counted_card'     => 'decimal:2',
        'counted_transfer' => 'decimal:2',
        'difference'       => 'decimal:2',
        'opened_at'     => 'datetime',
        'closed_at'     => 'datetime',
    ];

    const STATUS_OPEN      = 'open';
    const STATUS_CLOSED    = 'closed';
    const STATUS_CANCELLED = 'cancelled';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pettyCashVouchers(): HasMany
    {
        return $this->hasMany(PettyCashVoucher::class);
    }

    public function cashMovements(): HasMany
    {
        return $this->hasMany(CashMovement::class);
    }

    public function isOpen(): bool
    {
        return $this->status === self::STATUS_OPEN;
    }

    public function getStatusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_OPEN      => 'Abierto',
            self::STATUS_CLOSED    => 'Cerrado',
            self::STATUS_CANCELLED => 'Cancelado',
            default                => $this->status,
        };
    }

    public function scopeOpen($query)
    {
        return $query->where('status', self::STATUS_OPEN);
    }

    public function sumByType(string $type): float
    {
        return (float) $this->cashMovements->where('type', $type)->sum('amount');
    }

    public function cashSales(): float
    {
        return $this->sumByType(CashMovement::TYPE_VENTA_EFECTIVO);
    }

    public function cardSales(): float
    {
        return $this->sumByType(CashMovement::TYPE_VENTA_TARJETA);
    }

    public function transferSales(): float
    {
        return $this->sumByType(CashMovement::TYPE_VENTA_TRANSFERENCIA);
    }

    public function creditSales(): float
    {
        return $this->sumByType(CashMovement::TYPE_VENTA_CREDITO);
    }

    public function manualMovements()
    {
        return $this->cashMovements->whereIn('type', CashMovement::MANUAL_TYPES);
    }

    public function pettyCashMovements()
    {
        return $this->cashMovements->where('type', CashMovement::TYPE_VALE_CAJA_CHICA);
    }

    public function cardDifference(): ?float
    {
        return is_null($this->counted_card) ? null : (float) $this->counted_card - $this->cardSales();
    }

    public function transferDifference(): ?float
    {
        return is_null($this->counted_transfer) ? null : (float) $this->counted_transfer - $this->transferSales();
    }

    /**
     * Pedidos pagados durante este corte. No hay pos_shift_id en pos_orders:
     * se llega a la orden a través del pago (CashMovement::reference).
     */
    public function orders()
    {
        return $this->cashMovements
            ->pluck('reference')
            ->filter(fn ($ref) => $ref instanceof PosPayment)
            ->pluck('order')
            ->filter()
            ->unique('id');
    }

    /**
     * Pedidos que incluyeron al menos un pago a crédito. Se muestran aparte
     * porque no representan efectivo/tarjeta/transferencia recibido en caja.
     */
    public function creditOrders()
    {
        return $this->cashMovements
            ->where('type', CashMovement::TYPE_VENTA_CREDITO)
            ->pluck('reference')
            ->filter(fn ($ref) => $ref instanceof PosPayment)
            ->pluck('order')
            ->filter()
            ->unique('id');
    }

    /**
     * Pedidos del corte que NO se pagaron (ni parcialmente) a crédito.
     * Es la base para el desglose por tipo de orden y el total de ventas.
     */
    public function nonCreditOrders()
    {
        $creditIds = $this->creditOrders()->pluck('id');

        return $this->orders()->reject(fn ($order) => $creditIds->contains($order->id));
    }

    /**
     * Pedidos con descuento aplicado, pagados durante este corte (sin incluir crédito).
     */
    public function discountedOrders()
    {
        return $this->nonCreditOrders()->filter(fn ($order) => !empty($order->discount_code));
    }

    public function ordersGroupedByType(): array
    {
        $grouped = $this->nonCreditOrders()->groupBy('order_type');

        return collect(['dine_in', 'takeout', 'delivery'])
            ->mapWithKeys(fn ($type) => [$type => $grouped->get($type, collect())->values()])
            ->all();
    }

    public function orderTypeSummary(): array
    {
        return collect($this->ordersGroupedByType())
            ->map(fn ($orders) => [
                'count' => $orders->count(),
                'total' => (float) $orders->sum('total'),
            ])
            ->all();
    }
}
