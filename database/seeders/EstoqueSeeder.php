<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class EstoqueSeeder extends Seeder
{
    public function run(): void
    {
        \App\Models\Estoque::factory()->count(500)->create();
    }
}
