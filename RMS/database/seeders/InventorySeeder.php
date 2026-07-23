<?php

namespace Database\Seeders;

use App\Models\UnitOfMeasure;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class InventorySeeder extends Seeder
{
    public function run(): void
    {
        Warehouse::firstOrCreate(
            ['slug' => 'bodega-general'],
            ['name' => 'Bodega general', 'status' => 'active']
        );

        $gramo = UnitOfMeasure::firstOrCreate(
            ['abbreviation' => 'g'],
            ['name' => 'Gramo', 'base_unit_id' => null, 'conversion_factor' => 1]
        );

        UnitOfMeasure::firstOrCreate(
            ['abbreviation' => 'kg'],
            ['name' => 'Kilogramo', 'base_unit_id' => $gramo->id, 'conversion_factor' => 1000]
        );

        $mililitro = UnitOfMeasure::firstOrCreate(
            ['abbreviation' => 'ml'],
            ['name' => 'Mililitro', 'base_unit_id' => null, 'conversion_factor' => 1]
        );

        UnitOfMeasure::firstOrCreate(
            ['abbreviation' => 'l'],
            ['name' => 'Litro', 'base_unit_id' => $mililitro->id, 'conversion_factor' => 1000]
        );

        UnitOfMeasure::firstOrCreate(
            ['abbreviation' => 'pza'],
            ['name' => 'Pieza', 'base_unit_id' => null, 'conversion_factor' => 1]
        );
    }
}
