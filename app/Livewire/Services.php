<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Salon;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On; 

class Services extends Component
{
    public $editedServiceIndex = null;
    public $editedServiceField = null;
    public $index ='0';
    public $salon_serv = [];
    public $arr_salon =[];
        public function mount(){
               
       $services = Service::all();
       $salon_serv =[];
       $i=0; 
        
        
        if(isset($services)){
            foreach ($services as $service) {  
                $salon_serv[$i]["id"] = $service->id;
                $salon_serv[$i]["service"] = $service->service;
                $salon_serv[$i]["price"] = $service->price;
                $salon_serv[$i]["time"] = $service->time;                
                $i++;
            }
        }
        else{ 
                $salon_serv[$i]["id"] = '';
                $salon_serv[$i]["service"] = '';
                $salon_serv[$i]["price"] = '';
                $salon_serv[$i]["time"] = '';  

        }

        $this->salon_serv = $salon_serv;
        
       
    }
    
    public function editService($serviceIndex){
        $this->editedServiceIndex = $serviceIndex;
    }
    public function editServiceField($serviceIndex, $fieldName)
    {
        $this->editedServiceField = $serviceIndex.'.'.$fieldName;
    }
    public function updateService($serviceIndex)
    {  
        
        $serv = $this->salon_serv[$serviceIndex] ?? null;

        if (!is_null($serv) && !empty($serv['id'])) {   
                 
            $id = $serv['id'];            
            $service_updated= $serv['service'];            
            $price = $serv['price']; 
            $time = $serv['time'];
            
                      
            $serv_atualizado = Service::where('id',$id)
            ->update(['service' =>$service_updated,
             'price' => $price, 'time' => $time]);
            
            }
        if (!is_null($serv) && empty($serv['id'])) {   
                 
                       
                       
            $service_updated= $serv['service'];            
            $price = $serv['price']; 
            $time = $serv['time'];
            $inputs = ['service' => $serv['service'], 
            'price'=> $serv['price'], 'time' => $serv['time']];
                        
            Service::create($inputs);
            
            }  
            $this->editedServiceIndex = null;
            $this->editedServiceField = null;
            redirect('/dashboard');
        }
   
    public function deleteService($serviceIndex)
    {
       
        $service = $this->salon_serv[$serviceIndex] ?? null;
       
        if (!is_null($service['id'])) {   
                           
        Service::where('id','=', $service['id'])->delete();  
        redirect('/dashboard');
              
        }

    }
    public function addService($serviceIndex)
    {
        $this->editedServiceIndex = $serviceIndex;
        $salon_id = Auth::user()->salon_id;
        $services = Service::all();
        $salon_serv =[];
        $i=0;
        foreach ($services as $service) {
            $salon_serv[$i]["id"] = $service->id;
            $salon_serv[$i]["service"] = $service->service;
            $salon_serv[$i]["price"] = $service->price;
            $salon_serv[$i]["time"] = $service->time;
            
            $i++;
        }
        $i = $serviceIndex;
            $salon_serv[$i]["id"] = '';
            $salon_serv[$i]["service"] = '';
            $salon_serv[$i]["price"] = '';
            $salon_serv[$i]["time"] = '';
        
        $this->salon_serv = $salon_serv;
        
        
    }

    

    public function render()
    {
        return view('livewire.services',['salon_serv'=> $this->salon_serv]);
    }
}
