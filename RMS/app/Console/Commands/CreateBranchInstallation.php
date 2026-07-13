<?php

namespace App\Console\Commands;

use App\Models\Branch;
use App\Models\BranchInstallation;
use Illuminate\Console\Command;

/**
 * Out-of-band token issuance for a branch's local server. Run by
 * whoever is onboarding the branch (support/admin) — there is no
 * public self-service registration endpoint yet (see the installer
 * phase for that refinement). The printed token is shown once.
 */
class CreateBranchInstallation extends Command
{
    protected $signature = 'branch:create-installation {branch_id} {--device=}';

    protected $description = 'Emite un token de sincronización para el servidor local de una sucursal';

    public function handle(): int
    {
        $branch = Branch::find($this->argument('branch_id'));

        if (!$branch) {
            $this->error('Sucursal no encontrada.');
            return self::FAILURE;
        }

        $installation = BranchInstallation::create([
            'branch_id'   => $branch->id,
            'device_name' => $this->option('device'),
            'status'      => 'active',
        ]);

        $token = $installation->createToken('branch-server', ['sync:push', 'sync:pull'])->plainTextToken;

        $this->info('Instalación creada.');
        $this->line('branch_uuid:        ' . $branch->uuid);
        $this->line('company_uuid:       ' . $branch->company->uuid);
        $this->line('installation_uuid:  ' . $installation->uuid);
        $this->line('token:              ' . $token);
        $this->newLine();
        $this->comment('Coloca el token en el .env de POS como SYNC_API_TOKEN y corre sync:pull --full.');

        return self::SUCCESS;
    }
}
