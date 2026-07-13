<?php

namespace App\Console\Commands;

use App\Services\Sync\SyncPullService;
use Illuminate\Console\Command;

class SyncPullCommand extends Command
{
    protected $signature = 'sync:pull {--full : Ignora el último pull y descarga todo el catálogo}';

    protected $description = 'Descarga catálogo, usuarios y configuración desde el BackOffice';

    public function handle(SyncPullService $service): int
    {
        try {
            $result = $service->pull((bool) $this->option('full'));
        } catch (\Throwable $e) {
            $this->error('No se pudo completar el pull: ' . $e->getMessage());
            return self::FAILURE;
        }

        $this->info('Sucursal: ' . $result['branch']->name);
        foreach ($result['counts'] as $key => $count) {
            $this->line("  {$key}: {$count}");
        }

        return self::SUCCESS;
    }
}
