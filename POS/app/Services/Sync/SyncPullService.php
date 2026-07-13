<?php

namespace App\Services\Sync;

use App\Models\Branch;
use App\Models\Dish;
use App\Models\DishCategory;
use App\Models\PettyCashCategory;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class SyncPullService
{
    public function __construct(
        private InstallationClient $client,
        private LocalBranch $localBranch,
    ) {}

    public function pull(bool $full = false): array
    {
        $since = $full ? null : Cache::get('sync.last_pull_at');

        $response = $this->client->client()
            ->get('bootstrap', array_filter(['since' => $since]))
            ->throw()
            ->json();

        $data = $response['data'];

        $branch = Branch::updateOrCreate(
            ['uuid' => $data['branch']['uuid']],
            [
                'company_uuid' => $data['branch']['company_uuid'],
                'code'         => $data['branch']['code'],
                'name'         => $data['branch']['name'],
                'address'      => $data['branch']['address'],
                'city'         => $data['branch']['city'],
                'timezone'     => $data['branch']['timezone'],
                'status'       => $data['branch']['status'],
            ]
        );
        $this->localBranch->forgetCache();

        foreach ($data['dish_categories'] as $row) {
            DishCategory::updateOrCreate(['uuid' => $row['uuid']], $row);
        }

        foreach ($data['dishes'] as $row) {
            $categoryId = $row['dish_category_uuid']
                ? DishCategory::where('uuid', $row['dish_category_uuid'])->value('id')
                : null;

            Dish::updateOrCreate(['uuid' => $row['uuid']], [
                'dish_category_id' => $categoryId,
                'name'             => $row['name'],
                'description'      => $row['description'],
                'image_path'       => $row['image_path'],
                'price'            => $row['price'],
                'status'           => $row['status'],
            ]);
        }

        foreach ($data['users'] as $row) {
            User::updateOrCreate(['uuid' => $row['uuid']], [
                'name'     => $row['name'],
                'email'    => $row['email'],
                'password' => $row['password_hash'],
                'role'     => $row['role'],
                'status'   => $row['status'],
            ]);
        }

        foreach ($data['petty_cash_categories'] as $row) {
            PettyCashCategory::updateOrCreate(['uuid' => $row['uuid']], $row);
        }

        Cache::forever('sync.last_pull_at', $response['server_time']);

        return [
            'branch' => $branch,
            'counts' => [
                'dish_categories'       => count($data['dish_categories']),
                'dishes'                => count($data['dishes']),
                'users'                 => count($data['users']),
                'petty_cash_categories' => count($data['petty_cash_categories']),
            ],
        ];
    }
}
