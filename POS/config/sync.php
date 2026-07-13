<?php

return [

    // BackOffice (RMS) connection. The local branch's own identity
    // (id/uuid/company_uuid) is NOT kept here — it lives in the local
    // `branches` table, resolved via App\Services\Sync\LocalBranch, and
    // is populated by the first sync:pull --full after the token below
    // is issued (see RMS's branch:create-installation command).

    'backoffice_url' => env('SYNC_BACKOFFICE_URL', 'https://backoffice.donpulpo.example'),
    'api_token'       => env('SYNC_API_TOKEN'),

    // Cadence, in minutes, for the sync daemon / scheduler.
    'push_interval_minutes' => env('SYNC_PUSH_INTERVAL', 3),
    'pull_interval_minutes' => env('SYNC_PULL_INTERVAL', 1440), // once a day by default

    // Push batching and retry behavior.
    'push_batch_size'  => env('SYNC_PUSH_BATCH_SIZE', 50),
    'max_attempts'     => env('SYNC_MAX_ATTEMPTS', 20),
    'http_timeout'     => env('SYNC_HTTP_TIMEOUT', 5),
    'http_connect_timeout' => env('SYNC_HTTP_CONNECT_TIMEOUT', 5),

];
