<?php

namespace Database\Seeders;

use App\Models\DiningTable;
use Illuminate\Database\Seeder;

class DiningTableSeeder extends Seeder
{
    public function run(): void
    {
        // Buat 10 meja: T01..T10
        for ($i = 1; $i <= 10; $i++) {
            $code = 'T'.str_pad((string)$i, 2, '0', STR_PAD_LEFT);
            DiningTable::updateOrCreate(
                ['code' => $code],
                ['name' => 'Meja '.$i, 'capacity' => 2 + ($i % 3) * 2, 'status' => 'available']
            );
        }
    }
}
