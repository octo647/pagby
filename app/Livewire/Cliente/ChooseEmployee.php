<?php

namespace App\Livewire\Cliente;

use App\Models\Branch;
use Livewire\Component;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Schedule;
use Livewire\Attributes\On; 

class ChooseEmployee extends Component
{
    public $funcionarios = []; //array de funcionários
    public $chosen_services = [];
    public $ch_branch_name = null; //chosen branch name
    public $ch_branch_id = null; //chosen branch id
    public $choosedEmployee = null;

    public function mount()
    {
        $branches = Branch::all();
        if ($branches->count() === 1) {
            $branch = $branches->first();
            $this->chosenBranch($branch->branch_name);
        }
    }

    #[On('professional-restored')]
    public function restoreProfessional($professional)
    {
        \Log::info('ChooseEmployee: professional-restored event received', [
            'professional_id' => $professional,
        ]);
        
        $this->choosedEmployee = $professional;
        
        // Carregar o profissional e seu branch
        $employee = User::find($professional);
        if ($employee) {
            $branch = $employee->branches()->first();
            if ($branch) {
                $this->ch_branch_id = $branch->id;
                $this->ch_branch_name = $branch->branch_name;
                
                // Carregar funcionários do branch
                $this->chosenBranch($branch->branch_name);
            }
            
            \Log::info('ChooseEmployee: professional restored in UI', [
                'employee_name' => $employee->name,
                'branch' => $this->ch_branch_name,
            ]);
        }
    }

    #[On('chosen_branch')] 
    public function chosenBranch(string $chosen_branch): void
        {
            $branch = Branch::where('branch_name', $chosen_branch)->first();
            if (!$branch) {
                $this->funcionarios = [];
                return;
            }
            $ch_branch_id = $branch->id;
            // Identifica os funcionários associados ao branch escolhido e os serviços que eles oferecem
            $funcionarios = User::whereHas('branches', function($q) use ($ch_branch_id) {
                $q->where('branch_id', $ch_branch_id);
            })
            ->whereHas('roles', function($q) {
                $q->where('role', 'Funcionário');
            })
            ->with('branches')
            ->with('services')
            ->get();
            
            
            $this->funcionarios = $funcionarios;       
               
        }
    
    


    public function render()
    {
        return view('livewire.cliente.choose-employee', [
            'funcionarios' => $this->funcionarios,
        ]);

    }

    
    public function showServices($employee_id){
        $employee = User::find($employee_id);//pega o funcionário do salão
        $this->choosedEmployee = $employee_id;
        $services = $employee->services()->get();            
        $this->dispatch('chosen_professional', $employee_id);
        $this->dispatch('services', $services);
   }

     public function chosenService()
     {  
        
       
             
        //$this->chosen_services = $chs;
        $this->dispatch('ch_professional', $this->choosedEmployee);
        
        $this->dispatch('ch_services', $this->chosen_services);
    
        //$this->ch_branch_name = null;
       
     }
    public function apague(){
         $this->choosedEmployee = null;
  
         $this->chosen_services = [];// Recarrega os funcionários do branch selecionado, se branch estiver definido
    
}
         

    /*
     * @param array $agends
     */
    public function setAgends(array $agends): void
    {
        $this->agends = $agends;
    }
    public function selectProfessional($employee_id)
    {
        $this->choosedEmployee = $employee_id;
        $this->dispatch('chosen_professional', $employee_id);
        
    }

   


}
