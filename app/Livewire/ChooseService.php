<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On; 
use App\Models\Service;
use App\Models\User;




class ChooseService extends Component
{
    public $services = null;
    public $chosen_services = [];
    public $ch_services = [];
    public $ch_professional = null;

    #[On('services')] 
    public function offeredServices($services){
        $this->services= $services;
    }
    #[On('chosen_professional')] 
    public function ch_professional($chosen_professional){
        $this->ch_professional= $chosen_professional;
    }

    public function chosen(){
        $professional =User::find($this->ch_professional);
        $services = Service::whereIn('id', $this->chosen_services)->get();
        if (!$professional) {
            $this->dispatch('error', 'Nenhum profissional selecionado.');
            return;
        }
        if ($services->isEmpty()) {
            $this->dispatch('error', 'Nenhum serviço selecionado.');
            return;
        }
        $ch_services = [
    'professional' => [
        'id' => $professional->id,
        'name' => $professional->name,
        'branch_id' => $professional->branch_id ?? null,
    ],
    'services' => $services->map(function($service) {
        return [
            'id' => $service->id,
            'name' => $service->service,
            // adicione outros campos se quiser
        ];
    })->toArray(),
];
        $this->ch_services = $ch_services;
        $this->dispatch('ch_professional', $this->ch_professional);
        $this->dispatch('ch_services', $this->ch_services); 
    }
     
    
    public function render()
    {
        return view('livewire.choose-service');
    }
}
