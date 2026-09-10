<?php

namespace Database\Seeders;

use App\Models\PosTable;
use Illuminate\Database\Seeder;

class PosTableSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1; $i <= 9; $i++) {
            PosTable::firstOrCreate(
                ['name' => "Mesa {$i}"],
                ['capacity' => 4, 'display_order' => $i],
            );
        }
    }
}
