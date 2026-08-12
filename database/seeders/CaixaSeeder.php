<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Comanda;

class CaixaSeeder extends Seeder
{
    public function run(): void
    {
        // Agrupa comandas por branch_id e data (apenas Y-m-d)
        $comandas = Comanda::all()->groupBy(function ($comanda) {
            return $comanda->branch_id . '|' . date('Y-m-d', strtotime($comanda->data_abertura));
        });
       

        foreach ($comandas as $key => $grupo) {
            [$branch_id, $data_abertura] = explode('|', $key);
            $branch_id = (int) $branch_id;
            $total = 0;
            $created_at = null;
            $updated_at = null;

            foreach ($grupo as $comanda) {
                $totalServicos = DB::table('comanda_servicos')->where('comanda_id', $comanda->id)->sum('subtotal');
                $totalProdutos = DB::table('comanda_produtos')->where('comanda_id', $comanda->id)->sum('subtotal');
                $total += ($totalServicos + $totalProdutos);
                if (!$created_at) $created_at = $comanda->created_at;
                if (!$updated_at) $updated_at = $comanda->updated_at;
            }

            DB::table('caixa')->updateOrInsert(
                [
                    'branch_id' => $branch_id,
                    'data' => date('Y-m-d', strtotime($data_abertura)),
                ],
                [
                    'created_at' => $created_at,
                    'updated_at' => $updated_at,
                    'total_entrada' => $total,
                    'total_saida' => 0,
                    'saldo_final' => $total,
                ]
            );
        }
    }
}
