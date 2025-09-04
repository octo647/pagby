<?php

namespace App\Livewire\Proprietario;

use Livewire\Component;
use App\Models\Salon;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On; 
use Livewire\WithFileUploads;

class Services extends Component
{
    public $editedServiceIndex = null;
    public $editedServiceField = null;
    public $index ='0';
    public $salon_serv = [];
    public $arr_salon =[];
    use WithFileUploads;

    public $photo;


    public function mount(){
               
       $services = Service::all();
       $salon_serv =[];

       
        
        
        if(isset($services)){
         
            foreach ($services as $service) {
                $salon_serv[]= [
                "id" => $service->id,
                "photo" => $service->photo  ?? '',
                "service" => $service->service,
                "price" => $service->price,
                "time" => $service->time, 
                'tenant' => Auth::user()->salon_id ?? 'default',     
                ];
            }
        }
        else{ 
                $salon_serv[] = [
                "id" => '',
                "photo" => '',
                "service" => '',
                "price" => '',
                "time" => '', 
                'tenant' => Auth::user()->salon_id ?? 'default',
                 
                ];
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
        if (!isset($this->salon_serv[$serviceIndex])) {
            return;
        }
        $serv = $this->salon_serv[$serviceIndex];

        $photoName = null;

        // Se for upload novo
        if (isset($serv['photo']) && is_object($serv['photo'])) {
            $photoName = $serv['photo']->getClientOriginalName();
            $serv['photo']->storeAs('services', $photoName, 'public');
            $this->salon_serv[$serviceIndex]['photo'] = $photoName;
        } elseif (!empty($serv['photo'])) {
            // Foto já existente (string)
            $photoName = $serv['photo'];
        }

        $inputs = [
            'service' => $serv['service'],
            'photo' => $photoName,
            'price' => $serv['price'],
            'time' => $serv['time'],
        ];

        if (!empty($serv['id']) && is_numeric($serv['id'])) {
            Service::where('id', $serv['id'])->update($inputs);
        } else {
            $newService = Service::create($inputs);
            $this->salon_serv[$serviceIndex]['id'] = $newService->id;
        }

        $this->editedServiceIndex = null;
        $this->editedServiceField = null;
}
   
    public function deleteService($serviceIndex)
    {
       
        $service = $this->salon_serv[$serviceIndex] ?? null;
     
       
        if (!is_null($service['id'])) {                              
        Service::where('id','=', $service['id'])->delete();             
        }
        // Remove do array local
        unset($this->salon_serv[$serviceIndex]);
        $this->salon_serv = array_values($this->salon_serv); // <-- reindexa!

        // Opcional: feche edição se necessário
        $this->editedServiceIndex = null;

    }
    public function addService($serviceIndex)
    {
        $this->editedServiceIndex = $serviceIndex;
        $salon_id = Auth::user()->salon_id;
        $services = Service::all();
        
        $salon_serv =[];
       
        foreach ($services as $service) {
            
            $salon_serv[] = [
                "id" => $service->id,
                "photo" => $service->photo  ?? '',
                "service" => $service->service,
                "price" => $service->price,
                "time" => $service->time,                
            ];            
        }
        
        if (empty($salon_serv)) {
            $salon_serv[] = [
                "id" => '',
                "photo" => '',
                "service" => '',
                "price" => '',
                "time" => '',                
            ];
        }
        // Adiciona um novo serviço vazio ao array
        $this->salon_serv[] = [
            "id" => '',
            "photo" => '', // Pode ser uma string vazia ou null
            "service" => '',
            "price" => '',
            "time" => '',                
        ];
        $this->salon_serv = array_values($this->salon_serv); // <-- reindexa!
        $this->dispatch('serviceAdded'); // Dispara o evento para notificar o componente pai      
        
    }

    

    public function render()
    {
        return view('livewire.proprietario.services',['salon_serv'=> $this->salon_serv, 'photo' => $this->photo]);

    }
}
