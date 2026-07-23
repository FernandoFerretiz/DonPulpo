<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\DB;

class StockMovement extends Model
{
    protected $fillable = [
        'folio', 'inventory_product_id', 'warehouse_id', 'related_warehouse_id',
        'type', 'quantity', 'unit_cost', 'previous_balance', 'new_balance',
        'reference_type', 'reference_id', 'user_id', 'notes', 'movement_date',
    ];

    protected function casts(): array
    {
        return [
            'quantity'          => 'decimal:3',
            'unit_cost'         => 'decimal:4',
            'previous_balance'  => 'decimal:3',
            'new_balance'       => 'decimal:3',
            'movement_date'     => 'datetime',
        ];
    }

    const TYPE_PURCHASE             = 'purchase';
    const TYPE_SALE                 = 'sale';
    const TYPE_PRODUCTION           = 'production';
    const TYPE_ADJUSTMENT           = 'adjustment';
    const TYPE_SHRINKAGE            = 'shrinkage';
    const TYPE_TRANSFER             = 'transfer';
    const TYPE_INTERNAL_CONSUMPTION = 'internal_consumption';
    const TYPE_RETURN               = 'return';
    const TYPE_CANCELLATION         = 'cancellation';
    const TYPE_INITIAL_STOCK        = 'initial_stock';

    const TYPES = [
        self::TYPE_PURCHASE, self::TYPE_SALE, self::TYPE_PRODUCTION, self::TYPE_ADJUSTMENT,
        self::TYPE_SHRINKAGE, self::TYPE_TRANSFER, self::TYPE_INTERNAL_CONSUMPTION,
        self::TYPE_RETURN, self::TYPE_CANCELLATION, self::TYPE_INITIAL_STOCK,
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(InventoryProduct::class, 'inventory_product_id');
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function relatedWarehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'related_warehouse_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

    public function getTypeLabel(): string
    {
        return match ($this->type) {
            self::TYPE_PURCHASE             => 'Compra',
            self::TYPE_SALE                 => 'Venta',
            self::TYPE_PRODUCTION           => 'Producción',
            self::TYPE_ADJUSTMENT           => 'Ajuste',
            self::TYPE_SHRINKAGE            => 'Merma',
            self::TYPE_TRANSFER             => 'Transferencia',
            self::TYPE_INTERNAL_CONSUMPTION => 'Consumo interno',
            self::TYPE_RETURN               => 'Devolución',
            self::TYPE_CANCELLATION         => 'Cancelación',
            self::TYPE_INITIAL_STOCK        => 'Inventario inicial',
            default                          => $this->type,
        };
    }

    public static function generateFolio(): string
    {
        $prefix = 'MOV-' . now()->format('Ymd') . '-';
        $last   = static::where('folio', 'like', $prefix . '%')->orderByDesc('id')->first();
        $seq    = $last ? ((int) substr($last->folio, -4)) + 1 : 1;
        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Único punto de entrada para modificar existencias. Nunca escribir
     * warehouse_stocks directamente fuera de este método: aquí se calcula
     * el saldo, se deja el rastro en el kardex y se actualiza el cache.
     *
     * $quantity es el delta con signo: positivo incrementa existencias,
     * negativo las disminuye (compras/producción/devoluciones/inventario
     * inicial son positivos; ventas/mermas/consumo interno/cancelación son
     * negativos; ajustes y transferencias llevan el signo que corresponda
     * al caso concreto, ya que pueden ir en cualquier dirección).
     */
    public static function record(array $data): self
    {
        return DB::transaction(function () use ($data) {
            $productId   = $data['inventory_product_id'];
            $warehouseId = $data['warehouse_id'];
            $quantity    = (float) $data['quantity'];

            if ($quantity === 0.0) {
                throw new \InvalidArgumentException('El movimiento de inventario no puede tener cantidad cero.');
            }

            $stock = WarehouseStock::query()
                ->where('inventory_product_id', $productId)
                ->where('warehouse_id', $warehouseId)
                ->lockForUpdate()
                ->first();

            if (! $stock) {
                $stock = WarehouseStock::create([
                    'inventory_product_id' => $productId,
                    'warehouse_id'          => $warehouseId,
                    'quantity'              => 0,
                ]);
            }

            $previousBalance = (float) $stock->quantity;
            $newBalance      = $previousBalance + $quantity;

            $movement = static::create([
                'folio'                => $data['folio'] ?? static::generateFolio(),
                'inventory_product_id' => $productId,
                'warehouse_id'         => $warehouseId,
                'related_warehouse_id' => $data['related_warehouse_id'] ?? null,
                'type'                 => $data['type'],
                'quantity'             => $quantity,
                'unit_cost'            => $data['unit_cost'] ?? 0,
                'previous_balance'     => $previousBalance,
                'new_balance'          => $newBalance,
                'reference_type'       => $data['reference_type'] ?? ($data['reference'] ?? null)?->getMorphClass(),
                'reference_id'         => $data['reference_id'] ?? ($data['reference'] ?? null)?->getKey(),
                'user_id'              => $data['user_id'] ?? auth()->id(),
                'notes'                => $data['notes'] ?? null,
                'movement_date'        => $data['movement_date'] ?? now(),
            ]);

            $stock->update(['quantity' => $newBalance]);

            return $movement;
        });
    }
}
