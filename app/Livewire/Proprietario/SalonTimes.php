<?php

namespace App\Livewire\Proprietario;

use App\Models\Role;
use Livewire\Component;
use App\Models\Schedule;
use App\Models\User;
use App\Models\BranchUser;
class SalonTimes extends Component
{

    public $editedIndex = null;
    public $editedField = null;
    public array $officehours = [];
    public $showEditPanel = false;
    public $editOfficehour = [];
    public $editIndex = null;
    public $tabelaAtiva = 'usuarios';

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
        $schedules = Schedule::all()->groupBy('user_id');
        $funcionarios = User::whereHas('roles', function ($query) {
            $query->where('role', 'Funcionário');
        })->get();
       
        $officehours = [];
        foreach ($funcionarios as $func) {
               $linha = [
        'id' => $func->id,
        'funcionario' => $func->name,
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
            $dia = $sch->day_of_week; 
            // Map English day names to Portuguese abbreviations
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

        $this->officehours = $officehours;    
        
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
                        'lunch_start' => $lunch_ini, // Default lunch start time
                        'lunch_end' => $lunch_end, // Default lunch end time
                        'updated_at' => now(),
                        'status' => 'active', // Default status
                    ]
                );
            }else {
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
    $this->mount(); // Recarrega os dados
    }
    public function deleteMT($mtIndex)
    {
        $officehour = $this->officehours[$mtIndex] ?? null;
        $mt = Schedule::find($officehour['id']);
        $mt->delete();
        redirect('/dashboard');

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
        return view('livewire.proprietario.salon-times');
    }
 

}
