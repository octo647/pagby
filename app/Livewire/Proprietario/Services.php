<?php

namespace App\Livewire\Proprietario;

use Livewire\Component;
use App\Models\Salon;
use App\Models\Service;
use App\Models\Branch;
use App\Models\BranchService;
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
    public $branches = [];
    public $branchServices = [];
    public $showBranchPricing = [];
    public $branchPrices = [];
    public $branchDurations = [];
    use WithFileUploads;

    public $photo;


    public function mount(){
               
       $services = Service::all();
       $salon_serv =[];

       // Carregar filiais
       $this->branches = Branch::all();
       
       // Carregar configurações de serviços por filial
       $this->loadBranchServices();
        
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
                
                // Inicializar mostrar preços por filial como false
                $this->showBranchPricing[$service->id] = false;
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

    

    /**
     * Carregar configurações de serviços por filial
     */
    public function loadBranchServices()
    {
        $branchServices = BranchService::with(['branch', 'service'])->get();
        
        $this->branchServices = [];
        
        foreach ($branchServices as $bs) {
            $this->branchServices[$bs->service_id][$bs->branch_id] = [
                'price' => $bs->price,
                'duration_minutes' => $bs->duration_minutes,
                'is_active' => $bs->is_active,
                'branch_name' => $bs->branch->branch_name,
                'service_name' => $bs->service->service
            ];
        }
    }
    
    /**
     * Alternar exibição de preços por filial
     */
    public function toggleBranchPricing($serviceId)
    {
        $this->showBranchPricing[$serviceId] = !($this->showBranchPricing[$serviceId] ?? false);
        
        // Se estiver abrindo a seção, inicializar os valores dos campos
        if ($this->showBranchPricing[$serviceId]) {
            $this->initializeBranchFields($serviceId);
        }
    }
    
    /**
     * Inicializar campos com valores existentes da configuração por filial
     */
    private function initializeBranchFields($serviceId)
    {
        // Garantir que os arrays existem
        if (!isset($this->branchPrices[$serviceId])) {
            $this->branchPrices[$serviceId] = [];
        }
        if (!isset($this->branchDurations[$serviceId])) {
            $this->branchDurations[$serviceId] = [];
        }
        
        foreach ($this->branches as $branch) {
            $branchId = $branch->id;
            
            // Se já existe configuração para esta filial, inicializar com os valores existentes
            if (isset($this->branchServices[$serviceId][$branchId])) {
                $this->branchPrices[$serviceId][$branchId]['price'] = $this->branchServices[$serviceId][$branchId]['price'];
                $this->branchDurations[$serviceId][$branchId]['duration'] = $this->branchServices[$serviceId][$branchId]['duration_minutes'];
            } else {
                // Inicializar com valores vazios se não existe configuração
                if (!isset($this->branchPrices[$serviceId][$branchId])) {
                    $this->branchPrices[$serviceId][$branchId]['price'] = '';
                }
                if (!isset($this->branchDurations[$serviceId][$branchId])) {
                    $this->branchDurations[$serviceId][$branchId]['duration'] = '';
                }
            }
        }
    }
    
    /**
     * Obter preço de um serviço para uma filial específica
     */
    public function getBranchPrice($serviceId, $branchId)
    {
        return $this->branchServices[$serviceId][$branchId]['price'] ?? '';
    }
    
    /**
     * Obter duração de um serviço para uma filial específica
     */
    public function getBranchDuration($serviceId, $branchId)
    {
        return $this->branchServices[$serviceId][$branchId]['duration_minutes'] ?? '';
    }
    
    /**
     * Salvar configuração específica por filial
     */
    public function saveBranchConfiguration($serviceId, $branchId)
    {
        $price = $this->branchPrices[$serviceId][$branchId]['price'] ?? null;
        $duration = $this->branchDurations[$serviceId][$branchId]['duration'] ?? null;
        
        // Validações básicas
        if (empty($price) && empty($duration)) {
            session()->flash('error', 'Preencha pelo menos o preço ou a duração para criar uma configuração específica da filial.');
            return;
        }
        
        if ($price && $price <= 0) {
            session()->flash('error', 'O preço deve ser maior que zero.');
            return;
        }
        
        if ($duration && $duration <= 0) {
            session()->flash('error', 'A duração deve ser maior que zero.');
            return;
        }
        
        // Obter o serviço para usar valores padrão se necessário
        $service = Service::find($serviceId);
        
        // Preparar dados para salvar
        $dataToSave = [
            'price' => $price ?: $service->price, // Se não informado, usa preço padrão
            'duration_minutes' => $duration ?: $service->time, // Se não informado, usa tempo padrão
            'is_active' => true
        ];
        
        $branchService = BranchService::updateOrCreate(
            [
                'service_id' => $serviceId,
                'branch_id' => $branchId
            ],
            $dataToSave
        );
        
        // Recarregar dados
        $this->loadBranchServices();
        
        // Limpar campos temporários
        unset($this->branchPrices[$serviceId][$branchId]);
        unset($this->branchDurations[$serviceId][$branchId]);
        
        session()->flash('message', 'Configuração da filial salva com sucesso!');
    }
    
    /**
     * Remover configuração específica da filial
     */
    public function removeBranchPrice($serviceId, $branchId)
    {
        BranchService::where('service_id', $serviceId)
            ->where('branch_id', $branchId)
            ->delete();
            
        // Recarregar dados
        $this->loadBranchServices();
        
        session()->flash('message', 'Configuração da filial removida com sucesso!');
    }

    public function render()
    {
        return view('livewire.proprietario.services',[
            'salon_serv'=> $this->salon_serv, 
            'photo' => $this->photo,
            'branches' => $this->branches,
            'branchServices' => $this->branchServices,
            'showBranchPricing' => $this->showBranchPricing
        ]);

    }
}
