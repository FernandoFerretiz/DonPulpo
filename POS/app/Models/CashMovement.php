<?php

namespace App\Models;

use App\Concerns\BelongsToCurrentBranch;
use App\Concerns\HasSyncableUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class CashMovement extends Model
{
    use HasSyncableUuid;
    use BelongsToCurrentBranch;

    protected $fillable = [
        'pos_shift_id', 'user_id', 'type', 'amount',
        'payment_method', 'description', 'reference_type', 'reference_id',
        'branch_id', 'sync_status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    // ── Movement type constants ───────────────────────────────────
    const TYPE_FONDO_INICIAL       = 'FONDO_INICIAL';
    const TYPE_VENTA_EFECTIVO      = 'VENTA_EFECTIVO';
    const TYPE_VENTA_TARJETA       = 'VENTA_TARJETA';
    const TYPE_VENTA_TRANSFERENCIA = 'VENTA_TRANSFERENCIA';
    const TYPE_INGRESO_MANUAL      = 'INGRESO_MANUAL';
    const TYPE_RETIRO_EFECTIVO     = 'RETIRO_EFECTIVO';
    const TYPE_VALE_CAJA_CHICA     = 'VALE_CAJA_CHICA';
    const TYPE_DEVOLUCION_EFECTIVO = 'DEVOLUCION_EFECTIVO';

    // Affect expected_cash positively
    const INCOME_TYPES = [
        self::TYPE_FONDO_INICIAL,
        self::TYPE_VENTA_EFECTIVO,
        self::TYPE_INGRESO_MANUAL,
    ];

    // Affect expected_cash negatively
    const EXPENSE_TYPES = [
        self::TYPE_RETIRO_EFECTIVO,
        self::TYPE_VALE_CAJA_CHICA,
        self::TYPE_DEVOLUCION_EFECTIVO,
    ];

    // Appear in report but do not move cash
    const REPORTING_TYPES = [
        self::TYPE_VENTA_TARJETA,
        self::TYPE_VENTA_TRANSFERENCIA,
    ];

    // Manual movements the cashier can register
    const MANUAL_TYPES = [
        self::TYPE_INGRESO_MANUAL,
        self::TYPE_RETIRO_EFECTIVO,
        self::TYPE_DEVOLUCION_EFECTIVO,
    ];

    public function shift(): BelongsTo
    {
        return $this->belongsTo(PosShift::class, 'pos_shift_id');
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
            self::TYPE_FONDO_INICIAL       => 'Fondo inicial',
            self::TYPE_VENTA_EFECTIVO      => 'Venta efectivo',
            self::TYPE_VENTA_TARJETA       => 'Venta tarjeta',
            self::TYPE_VENTA_TRANSFERENCIA => 'Venta transferencia',
            self::TYPE_INGRESO_MANUAL      => 'Ingreso manual',
            self::TYPE_RETIRO_EFECTIVO     => 'Retiro',
            self::TYPE_VALE_CAJA_CHICA     => 'Vale caja chica',
            self::TYPE_DEVOLUCION_EFECTIVO => 'Devolución',
            default                        => $this->type,
        };
    }

    public function isIncome(): bool
    {
        return in_array($this->type, self::INCOME_TYPES);
    }

    public function isExpense(): bool
    {
        return in_array($this->type, self::EXPENSE_TYPES);
    }
}
