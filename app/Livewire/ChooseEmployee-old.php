<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Schedule;

class ChooseEmployee extends Component
{
    public $employees =[];
    public $agends = [];
    //public $choosedEmployee = null;
    public $chosen_services = [];

    public $choosedEmployee = null;
    public function mount():void
    {

        $users = User::all();//identifica os usuários do salão
        $i=0;
        foreach($users as $user){
            if($user->hasRole('Funcionário')){ //identifica os funcionários do salão
                $employees[$i]['employee_id']=$user->id;
                $employees[$i]['employee_name']=$user->name;
                $employees[$i]['employee_photo']=$user->profile_photo_path;
                $services = $user->services()->get();//identifica os serviços de cada empregado
                $j=0;
                foreach($services as $service){
                    $employees[$i]['service'][$j]['service_id'] = $service->id;
                    $employees[$i]['service'][$j]['service'] = $service->service;
                    $employees[$i]['service'][$j]['service_time'] = $service->time;
                    $employees[$i]['service'][$j]['service_price'] = $service->price;
                    $j++;
                }
                $i++;
            }

        }
        $this->employees = $employees;
        $agends = Schedule::all()->toArray();
        // dd($agends);
        $this->agends = $agends;





    }


    public function render()
    {
        return view('livewire.choose-employee');

    }

    public function chooseEmployee($chosen){
        //dd($chosen);
         $this->choosedEmployee = $chosen;

        $this->chosen = $this->employees[$chosen]['employee_id'];
        //$this->dispatch('chosen', $this->chosen);


    }
     public function chosenService($chs)
     {

        $this->chosen_services = $chs;
        $this->dispatch('chosen_services', $this->chosen_services);
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
