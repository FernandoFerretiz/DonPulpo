<?php

namespace App\Services\Sync;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class InstallationClient
{
    public function client(): PendingRequest
    {
        return Http::baseUrl(rtrim(config('sync.backoffice_url'), '/') . '/api/v1/sync')
            ->withToken((string) config('sync.api_token'))
            ->acceptJson()
            ->timeout((int) config('sync.http_timeout', 5))
            ->connectTimeout((int) config('sync.http_connect_timeout', 5));
    }
}
