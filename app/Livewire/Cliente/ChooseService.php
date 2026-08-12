<?php


namespace App\Livewire\Cliente;
use Livewire\Component;
use Livewire\Attributes\On; 
use App\Models\Service;
use App\Models\User;
use App\Models\BranchUser; // Certifique-se de que o modelo BranchUser está correto




class ChooseService extends Component
{
    public $services = null;
    public $chosen_services = [];
    public $ch_services = [];
    public $ch_professional = null;

    public function mount()
    {
        \Log::info('ChooseService mount() called', [
            'chosen_services' => $this->chosen_services,
            'ch_services' => $this->ch_services,
        ]);
    }

    #[On('services-restored')]
    public function restoreServices($services)
    {
        \Log::info('ChooseService: services-restored event received', [
            'services' => $services,
            'is_array' => is_array($services),
            'count' => is_array($services) ? count($services) : 0,
        ]);
        
        // Garantir que é array
        if (!is_array($services)) {
            $services = [$services];
        }
        
        // Converter para inteiros para garantir compatibilidade com wire:model
        $this->chosen_services = array_map('intval', $services);
        $this->ch_services = $this->chosen_services;
        
        \Log::info('ChooseService: services restored in UI', [
            'chosen_services' => $this->chosen_services,
            'ch_services' => $this->ch_services,
        ]);
        
        // Não chamar chosen() aqui - deixar o usuário ver as seleções e clicar no botão
        // Apenas notificar que serviços foram restaurados
        $this->dispatch('serviceChoicesRestored');
        
        \Log::info('ChooseService: Services visually marked, waiting for user confirmation');
    }

    #[On('services')] 
    public function offeredServices($services){
        $this->services= $services;
    }
    #[On('chosen_professional')] 
    public function ch_professional($chosen_professional){
        $this->ch_professional= $chosen_professional;
    }

    private function getBranchId($userId)
    {
        return BranchUser::where('user_id', $userId)->value('branch_id');
    }

    public function chosen(){
        if (!$this->ch_professional) {
        $this->dispatch('error', 'Selecione um profissional antes de prosseguir.');
        return;
    }
 
        $professional = User::find($this->ch_professional);
        
       
        $services = Service::whereIn('id', $this->chosen_services)->get();
        if (!$professional) {
            $this->dispatch('error', 'Nenhum profissional selecionado.');
            return;
        }
        if ($services->isEmpty()) {
            $this->addError('semservico', 'Selecione pelo menos um serviço para prosseguir.');
            return;
        }
        $ch_services = [
            'professional' => [
                'id' => $professional->id,
                'name' => $professional->name,
                'branch_id' => $professional->branch_id ?? null,
                'branch_id' => $this->getBranchId($professional->id),
            ],
            'services' => $services->map(function($service) {
                return [
                    'id' => $service->id,
                    'name' => $service->service,
                    'time'=> $service->time //adicione outros campos se quiser
                ];
            })->toArray(),
        ];
       
  
        //$this->ch_professional = $ch_services['professional'];    
        //$this->ch_services = $ch_services['services'];
        $this->dispatch('ch_professional', $ch_services['services'], $ch_services['professional']);
        $this->dispatch('chosen_services', $ch_services['services'], $ch_services['professional']);
        $this->chosen_services = $services->pluck('id')->toArray();
        
        

        
    }
     
    
    public function render()
    {
        return view('livewire.cliente.choose-service');
    }
}
