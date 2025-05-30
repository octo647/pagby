<?php

namespace App\Livewire;

use App\Models\Branch;
use Livewire\Component;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Schedule;
use Livewire\Attributes\On; 

class ChooseEmployee extends Component
{
    public $employees =[];
    public $agends = [];
    //public $choosedEmployee = null;
    public $chosen_services = [];
    public $ch_branch_name = null; //chosen branch name
    public $ch_branch_id = null; //chosen branch id
    public $choosedEmployee = null;

    #[On('chosen_branch')] 
    public function chosenBranch(string $chosen_branch): void
        {
            $employees = [];
            $ch_branch_id = Branch::where('branch_name', $chosen_branch)->first()->id;  
           
            $users = User::all();//pega todos os usuários do salão
            
            $this->ch_branch_id = $ch_branch_id;
            $this->ch_branch_name = $chosen_branch;
            $i=0;  
            foreach ($users as $user) {
            $branch_id=$user->branches[0]->pivot->branch_id;
            
            if($user->hasRole('Funcionário') && $branch_id == $this->ch_branch_id) {

                    
                    $employees[$i]['employee_name']=$user->name;
                    $employees[$i]['employee_id']=$user->id;
                    $employees[$i]['branch_id']=$branch_id;
                    $employees[$i]['employee_photo']= "images/".$user->id.".jpeg";
                    $i++;

                }
                
            }  
            $this->employees = $employees;       
               
        }
    
    // public function mount():void
    // {
        
    //     $users = User::all();//identifica os usuários do salão
        
    //     //$branches = Branch::all();
    //     $employees = [];
       
    //     $i=0;
        
    //     foreach($users as $user){
    //         $branch_id=$user->branches[0]->pivot->branch_id;//branch_id do usuário
          
    //         if($user->hasRole('Funcionário') ){ //identifica os funcionários do salão
    //             $employees[$i]['employee_id']=$user->id;
    //             $employees[$i]['branch_id']=$user->branch_id;
           
    //             $employees[$i]['employee_name']=$user->name;
    //             $employees[$i]['employee_photo']=$user->profile_photo_path;
    //             $services = $user->services()->get();//identifica os serviços de cada empregado
    //             $j=0;
    //             foreach($services as $service){
    //                 $employees[$i]['service'][$j]['service_id'] = $service->id;
    //                 $employees[$i]['service'][$j]['service'] = $service->service;
    //                 $employees[$i]['service'][$j]['service_time'] = $service->time;
    //                 $employees[$i]['service'][$j]['service_price'] = $service->price;
    //                 $j++;
    //             }
    //             $i++;
    //         }
            

    //     }
        
    //     $this->employees = $employees;
    //     $agends = Schedule::all()->toArray();
    //     // dd($agends);
    //     $this->agends = $agends;





    // }


    public function render()
    {
        return view('livewire.choose-employee');

    }

    public function chooseEmployee($chosen){
        
         
         
        //$this->chosen = $this->employees[$chosen]['employee_id'];
        //$this->dispatch('chosen', $this->chosen);


    }
    public function showServices($employee_id){
        $employee = User::find($employee_id);//pega o funcionário do salão
        $this->choosedEmployee = $employee_id;
        $services = $employee->services()->get();
            
       $this->dispatch('chosen_professional', $employee_id);
       $this->dispatch('services', $services);
       


   }

     public function chosenService($chs)
     {  
        
        $chs = unserialize($chs);
       
             
        $this->chosen_services = $chs;
        
        
        $this->dispatch('chosen_services', $this->chosen_services);
       
        $this->chosen_branch = null;
       
     }
    public function apague(){
         $this->choosedEmployee = null;
         $this->chosen_services = [];


    }

    /*
     * @param array $agends
     */
    public function setAgends(array $agends): void
    {
        $this->agends = $agends;
    }

   


}
