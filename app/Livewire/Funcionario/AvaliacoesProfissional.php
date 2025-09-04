<?php

namespace App\Livewire\Funcionario;

use Livewire\Component;
use App\Models\Avaliacao;
use Illuminate\Support\Facades\Auth;

class AvaliacoesProfissional extends Component
{
    public $avaliacoes=[];
    public $funcionario;
    public $funcionarioId;
    
    public $tabelaAtiva = 'avaliacoes';


    public function getAvaliacaoProperty()
    {   
        //obter o funcionário logado
        $this->funcionario = Auth::user();
        //verificar os appointment_id do funcionário logado
        $appointmentIds = $this->funcionario->appointments->pluck('id')->toArray();
        //se não houver appointment_id, retornar vazio
        if (empty($appointmentIds)) {
            return collect();
        }
        //buscar as avaliações do funcionário logado
        //e incluir os relacionamentos user_id e appointment_id
        //ordenar por data de criação, do mais recente para o mais antigo
        //retornar as avaliações
        //se não houver avaliações, retornar vazio
         
         

        
        
        return Avaliacao::whereIn('appointment_id', $appointmentIds)            
            ->orderBy('created_at', 'desc')
            ->get();
    }   

    public function render()
    {   
        $this->avaliacoes = $this->getAvaliacaoProperty();
        
        return view('livewire.funcionario.avaliacoes-profissional');
    }
}
