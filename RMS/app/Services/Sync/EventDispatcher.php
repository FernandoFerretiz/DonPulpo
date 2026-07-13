<?php

namespace App\Services\Sync;

use App\Models\Branch;
use App\Services\Sync\Handlers\CashMovementEventHandler;
use App\Services\Sync\Handlers\PettyCashVoucherEventHandler;
use App\Services\Sync\Handlers\PosOrderEventHandler;
use App\Services\Sync\Handlers\PosPaymentEventHandler;
use App\Services\Sync\Handlers\PosShiftEventHandler;

class EventDispatcher
{
    private const HANDLERS = [
        'PosOrder'         => PosOrderEventHandler::class,
        'PosPayment'       => PosPaymentEventHandler::class,
        'PosShift'         => PosShiftEventHandler::class,
        'CashMovement'     => CashMovementEventHandler::class,
        'PettyCashVoucher' => PettyCashVoucherEventHandler::class,
    ];

    public function dispatch(string $aggregateType, array $payload, Branch $branch): void
    {
        $handlerClass = self::HANDLERS[$aggregateType] ?? null;

        if (!$handlerClass) {
            throw new \RuntimeException("No hay handler de sync para el tipo de agregado [{$aggregateType}].");
        }

        app($handlerClass)->handle($payload, $branch);
    }
}
