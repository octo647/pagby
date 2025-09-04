<?php

namespace App\Livewire\Proprietario;

use Livewire\Component;

class RankingServicos extends Component
{   

    public $rankingServicos = [];

public function mount()
{
    $servicos = \App\Models\Appointment::pluck('services'); // retorna uma Collection de strings

    $contagem = [];

    foreach ($servicos as $lista) {
        if (!$lista) continue;
        foreach (explode('/', $lista) as $servico) {
            $servico = trim($servico);
            if ($servico === '') continue;
            if (!isset($contagem[$servico])) {
                $contagem[$servico] = 0;
            }
            $contagem[$servico]++;
        }
    }

    // Ordena do mais para o menos solicitado
    arsort($contagem);

    // Pega os 10 mais
    $this->rankingServicos = array_slice($contagem, 0, 10, true);
}
    public function render()
    {
        return view('livewire.proprietario.ranking-servicos');
    }
}
