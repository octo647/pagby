<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Branch;


class BranchesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Conta quantos branches já existem
        $count = Branch::count();

        // Só cria se houver menos de 3
        if ($count < 3) {
            Branch::factory(3 - $count)->create();
        }
    }
}
