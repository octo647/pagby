<?php

namespace App\Livewire\Proprietario;

use Livewire\Component;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;
use App\Models\User;


class EmployeeService extends Component
{

    public $services = [];
    public $employees = [];
    public $employee_services = [];
    public $selectedEmployeeId = null;
    protected $listeners = ['selectEmployeeFromMenu' => 'selectEmployee'];
    public function mount()
    {
        $users =User::all();
        //dd($user_salon);
        $i=0;
        $employees = [];
        $services = [];
        $services = Service::all()->toArray();
        $this->services = $services;
        $this->selectedEmployeeId = request()->input('funcionario_id');
        foreach($users as $index=>$user){


                if($user->hasRole('Funcionário')){
                    $employees[$i]['id']= $user->id;
                    $employees[$i]['name']= $user->name;
                    $employees[$i]['photo']= $user->photo;
                    $j=0;


                    foreach($services as $ind =>$service){

                        $service_id = $service['id'];

                        if($user->doService($service_id)){
                            $employees[$i]['services'][$j]= $service_id;
                            $j++;
                        }
                        else{
                            $employees[$i]['services'][$j]= '';
                            $j++;
                        }

                    }
                    $i++;
                }
            }
        

        $this->employees = $employees;

           


        }
    public function changeService($service_id, $employee_id){
        $user = User::find($employee_id);
        if($user->doService($service_id)){
            $user->resignService($service_id);
        }
        else{
            $user->assignService($service_id);
        }


        $this->mount();

        }
    public function selectEmployee($employeeId)
        {
            $this->selectedEmployeeId = $employeeId;
        }



    public function render()
    {

        return view('livewire.proprietario.employee-service', ['services'=>$this->services, 'employees'=>$this->employees]);


    }
}
