<?php
namespace App\Livewire;

use Livewire\Component;
use App\Models\Salon;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;


class AvailServices extends Component
{
    public $editedServiceIndex = null;
    public $editedServiceField = null;
    public $index ='0';
    public $salon_serv = [];
    public $selected_serv = [];
    public $selected = [];
    public $time= 0;

    public $chooseService = [];

   
    public function mount(){
        
        $salon_id = Auth::user()->salon_id;        
        $services = Service::where('salon_id', $salon_id )->get();
        $salon_serv =[];
        $selected_serv = [];
        $i=0;
        if(isset($services)){
            foreach ($services as $service) {  
                $salon_serv[$i]["id"] = $service->id;
                $salon_serv[$i]["salon_id"] = $salon_id;
                $salon_serv[$i]["service"] = $service->service;
                $salon_serv[$i]["price"] = $service->price;
                $salon_serv[$i]["time"] = $service->time;                
                $i++;
            }
        }
        else{ 
                $salon_serv[$i]["id"] = '';
                $salon_serv[$i]["salon_id"] = '';
                $salon_serv[$i]["service"] = '';
                $salon_serv[$i]["price"] = '';
                $salon_serv[$i]["time"] = '';  

        }


        $this->salon_serv = $salon_serv;
        $this->selected_serv = $selected_serv;
       // dd($salon_serv);
       
    }
    
    public function selectedServices(){
        $selecteds =$this->selected_serv;
        $salon_serv = $this->salon_serv;        
        $time_selected=0;
        foreach($selecteds as $selected) {
                  
            $time_selected += $salon_serv[$selected]['time'];
            
                      
        }
       
    }

    


    public function render()
    {
        return view('livewire.avail-services',['salon_serv'=> $this->salon_serv]);
    }
   
}