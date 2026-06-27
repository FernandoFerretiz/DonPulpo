<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // POS no siembra datos propios.
        // Los datos de users, dish_categories y dishes son sembrados por RMS.
        $this->command->info('POS no tiene seeders propios. Los datos vienen del RMS.');
    }
}
