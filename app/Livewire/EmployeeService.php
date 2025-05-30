<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;
use App\Models\User;


class EmployeeService extends Component
{

    public $services = [];
    public $employees = [];
    public $employee_services = [];
    public function mount()
    {
        $users =User::all();
        //dd($user_salon);
        $i=0;
        $employees = [];
        $services = [];
        $services = Service::all()->toArray();
        $this->services = $services;
        foreach($users as $index=>$user){


                if($user->hasRole('Funcionário')){
                    $employees[$i]['id']= $user->id;
                    $employees[$i]['name']= $user->name;
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


            redirect("/dashboard");
        }



    public function render()
    {
        return view('livewire.employee-service', ['services'=>$this->services, 'employees'=>$this->employees]);
        
    }
}
