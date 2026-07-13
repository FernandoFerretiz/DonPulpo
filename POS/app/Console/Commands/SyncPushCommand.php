<?php

namespace App\Console\Commands;

use App\Services\Sync\SyncPushService;
use Illuminate\Console\Command;

class SyncPushCommand extends Command
{
    protected $signature = 'sync:push {--batch=}';

    protected $description = 'Envía a la nube los eventos pendientes del outbox local';

    public function handle(SyncPushService $service): int
    {
        $batchSize = (int) ($this->option('batch') ?: config('sync.push_batch_size', 50));

        $result = $service->push($batchSize);

        $this->info("Enviados: {$result['sent']} | Confirmados: {$result['confirmed']} | Fallidos: {$result['failed']}");

        if (!empty($result['error'])) {
            $this->error($result['error']);
        }

        return self::SUCCESS;
    }
}
