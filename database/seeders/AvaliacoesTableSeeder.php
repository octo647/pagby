<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Avaliacao;

class AvaliacoesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verifica se a tabela de avaliações está vazia antes de inserir os dados
        if (\App\Models\Avaliacao::count() === 0) {
            // Insere avaliações padrão
            $avaliacoes = [
                ['user_id' => 1, 'branch_id' => 1, 'data' => now(), 'appointment_id' => 1, 'avaliacao' => 5, 'comentario' => 'Excelente serviço!'],
                ['user_id' => 2, 'branch_id' => 1, 'data' => now(), 'appointment_id' => 2, 'avaliacao' => 4, 'comentario' => 'Bom atendimento, mas poderia melhorar.'],
                ['user_id' => 3, 'branch_id' => 1, 'data' => now(), 'appointment_id' => 3, 'avaliacao' => 3, 'comentario' => 'Serviço regular.'],
                ['user_id' => 4, 'branch_id' => 1, 'data' => now(), 'appointment_id' => 4, 'avaliacao' => 2, 'comentario' => 'Não gostei do atendimento.'],
                ['user_id' => 5, 'branch_id' => 1, 'data' => now(), 'appointment_id' => 5, 'avaliacao' => 1, 'comentario' => 'Péssimo serviço!'],
                ['user_id' => 6, 'branch_id' => 1, 'data' => now(), 'appointment_id' => 6, 'avaliacao' => 5, 'comentario' => 'Adorei o corte de cabelo!'],
                ['user_id' => 7, 'branch_id' => 1, 'data' => now(), 'appointment_id' => 7, 'avaliacao' => 4, 'comentario' => 'Ótimo atendimento, mas o tempo de espera foi longo.'],
                ['user_id' => 8, 'branch_id' => 1, 'data' => now(), 'appointment_id' => 8, 'avaliacao' => 3, 'comentario' => 'Serviço bom, mas poderia ser mais rápido.'],
                ['user_id' => 9, 'branch_id' => 1, 'data' => now(), 'appointment_id' => 9, 'avaliacao' => 2, 'comentario' => 'Não gostei do resultado final.'],
                ['user_id' => 10, 'branch_id' => 1, 'data' => now(), 'appointment_id' => 10, 'avaliacao' => 1, 'comentario' => 'Não recomendo este salão.'],
                ['user_id' => 11, 'branch_id' => 1, 'data' => now(), 'appointment_id' => 11, 'avaliacao' => 5, 'comentario' => 'Excelente experiência!'],                
            ];
            // Insere as avaliações na tabela

            foreach ($avaliacoes as $avaliacao) {
                \App\Models\Avaliacao::firstOrCreate(
                    ['user_id' => $avaliacao['user_id'], 'branch_id' => $avaliacao['branch_id'], 'data' => $avaliacao['data']],
                    $avaliacao
                );
            }
        }
    }
}
