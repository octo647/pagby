<?php

namespace App\Livewire\Proprietario;

use App\Models\Role;
use Livewire\Component;
use App\Models\Schedule;
use App\Models\User;
use App\Models\BranchUser;
class SalonTimes extends Component
{
    // Limpa todos os horários de um dia da semana no painel de edição
    public function limparHorariosDia($dia)
    {
        if (!is_array($this->editOfficehour)) return;
        $this->editOfficehour[$dia . '_ini'] = null;
        $this->editOfficehour[$dia . '_fim'] = null;
        $this->editOfficehour[$dia . '_lunch_ini'] = null;
        $this->editOfficehour[$dia . '_lunch_fim'] = null;
    }

    public $editedIndex = null;
    public $editedField = null;
    public array $officehours = [];
    public $showEditPanel = false;
    public $editOfficehour = [];
    public $editIndex = null;
    public $tabelaAtiva = 'usuarios';
    public $showOnlyActive = true;

    public function abrirPainelEdicao($index)
    {
        $this->editIndex = $index;
        $this->editOfficehour = $this->officehours[$index];
        $this->showEditPanel = true;
        $edit = $this->officehours[$index];
       // dd($edit['seg_ini']);
    }

    public function salvarPainelEdicao()
    {
        $this->officehours[$this->editIndex] = $this->editOfficehour;
        $this->saveMT($this->editIndex); // Ou seu método de salvar
        $this->showEditPanel = false;
    }

    public function mount()
    {
        // Método mount simplificado - dados carregados dinamicamente no render
    }

 
    public function saveMT($mtIndex)
    {
        $officehour = $this->officehours[$mtIndex] ?? null;
        
        $diasMap = [
                'seg' => 'monday',
                'ter' => 'tuesday',
                'qua' => 'wednesday',
                'qui' => 'thursday',
                'sex' => 'friday',
                'sab' => 'saturday',
                'dom' => 'sunday'               
            ];
             if (!is_null($officehour)) {
        foreach ($diasMap as $pt => $en) {
            $ini = $officehour[$pt . '_ini'] ?? null;
            $fim = $officehour[$pt . '_fim'] ?? null;
            $lunch_ini = $officehour[$pt . '_lunch_ini'] ?? '12:00';
            $lunch_end = $officehour[$pt . '_lunch_fim'] ?? '13:00';
            $branchId = $officehour['branch_id'] ?? 1;

            // Corrige campos apagados: se vazio ou igual a '00:00', salva como null
            if (empty($ini) || $ini === '00:00') $ini = null;
            if (empty($fim) || $fim === '00:00') $fim = null;
            if (empty($lunch_ini) || $lunch_ini === '00:00') $lunch_ini = null;
            if (empty($lunch_end) || $lunch_end === '00:00') $lunch_end = null;

            if ($ini && $fim) {
                Schedule::updateOrCreate(
                    [
                        'user_id' => $officehour['id'],
                        'day_of_week' => $en,
                    ],
                    [   
                        'branch_id' => $branchId,
                        'start_time' => $ini,
                        'end_time' => $fim,
                        'lunch_start' => $lunch_ini,
                        'lunch_end' => $lunch_end,
                        'updated_at' => now(),
                        'status' => 'active',
                    ]
                );
            } else {
                // Se ambos vazios, remove o horário do banco
                Schedule::where([
                    'user_id' => $officehour['id'],
                    'day_of_week' => $en,
                    'branch_id' => $branchId,
                ])->delete();
            }
        }
    }

    $this->editedIndex = null;
    $this->editedField = null;
    // Dados serão recarregados automaticamente no próximo render
    }
    public function deleteMT($mtIndex)
    {
        $officehour = $this->officehours[$mtIndex] ?? null;
        $mt = Schedule::find($officehour['id']);
        $mt->delete();
        redirect('/dashboard');

    }
    private function getOfficeHours()
    {
        $schedules = Schedule::all()->groupBy('user_id');
        $funcionarios = User::whereHas('roles', function ($query) {
            $query->where('role', 'Funcionário');
        });
        
        if ($this->showOnlyActive) {
            $funcionarios->where('status', 'Ativo');
        }
        
        $funcionarios = $funcionarios->get();
       
        $officehours = [];
        foreach ($funcionarios as $func) {
            $linha = [
                'id' => $func->id,
                'funcionario' => $func->name,
                'photo' => $func->photo,
                'branch_id' => BranchUser::where('user_id',$func->id)->first()->branch_id ?? null,
                'seg_ini' => '', 'seg_fim' => '',
                'ter_ini' => '', 'ter_fim' => '',
                'qua_ini' => '', 'qua_fim' => '',
                'qui_ini' => '', 'qui_fim' => '',
                'sex_ini' => '', 'sex_fim' => '',
                'sab_ini' => '', 'sab_fim' => '',
                'dom_ini' => '', 'dom_fim' => '',
            ];

            if (isset($schedules[$func->id])) {
                foreach ($schedules[$func->id] as $sch) {
                    $diasMap = [
                        'monday' => 'seg',
                        'tuesday' => 'ter',
                        'wednesday' => 'qua',
                        'thursday' => 'qui',
                        'friday' => 'sex',
                        'saturday' => 'sab',
                        'sunday' => 'dom',
                    ];
                    $dia = $diasMap[strtolower($sch->day_of_week)] ?? $sch->day_of_week;
                    $linha[$dia . '_ini'] = date('H:i', strtotime($sch->start_time));
                    $linha[$dia . '_fim'] = date('H:i', strtotime($sch->end_time));
                    $linha[$dia . '_lunch_ini'] = date('H:i', strtotime($sch->lunch_start ?? '12:00'));
                    $linha[$dia . '_lunch_fim'] = date('H:i', strtotime($sch->lunch_end ?? '13:00'));
                }
            }

            $officehours[] = $linha;
        }

        return $officehours;
    }

    public function repetirSegunda()
    {
        $dias = ['ter', 'qua', 'qui', 'sex', 'sab', 'dom'];
        foreach ($dias as $dia) {
            $this->editOfficehour[$dia . '_ini'] = $this->editOfficehour['seg_ini'] ?? null;
            $this->editOfficehour[$dia . '_fim'] = $this->editOfficehour['seg_fim'] ?? null;
            $this->editOfficehour[$dia . '_lunch_ini'] = $this->editOfficehour['seg_lunch_ini'] ?? null;
            $this->editOfficehour[$dia . '_lunch_fim'] = $this->editOfficehour['seg_lunch_fim'] ?? null;
        }
        
    }
    public function render()
    {
        $this->officehours = $this->getOfficeHours();
        return view('livewire.proprietario.salon-times');
    }
 

}
