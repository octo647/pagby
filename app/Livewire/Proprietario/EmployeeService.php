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
    public $selectedEmployeeId = null;
    
    protected $listeners = ['selectEmployeeFromMenu' => 'selectEmployee'];
    public function mount()
    {
        try {
            // 1. Primeiro carrega os serviços
            $this->services = Service::select('id', 'service')->get()->toArray();
            
            // 2. Define o funcionário selecionado
            $this->selectedEmployeeId = request()->input('funcionario_id') ?? 246;
            
            // 3. Carrega funcionários usando a lógica original que funcionava
            $users = User::all();
            
            // 4. Monta o array de funcionários
            $this->employees = [];
            foreach($users as $user) {
                if($user->hasRole('Funcionário')) {
                    $employeeServices = $user->services()->pluck('service_id')->toArray();
                    
                    $this->employees[] = [
                        'id' => $user->id,
                        'name' => $user->name,
                        'photo' => $user->photo ?? 'default.jpg',
                        'services' => $employeeServices
                    ];
                }
            }
            

            
        } catch (\Exception $e) {
            error_log("Erro no mount: " . $e->getMessage());
            
            // Fallback para dados fixos em caso de erro
            $this->services = Service::select('id', 'service')->get()->toArray();
            $this->employees = [];
            $this->selectedEmployeeId = null;
        }
    }
    public function changeService($service_id, $employee_id)
    {
        try {
            $user = User::find($employee_id);
            $service = Service::find($service_id);
            
            if (!$user || !$service) {
                return;
            }
            
            // Verifica se o usuário já executa o serviço
            if($user->services()->where('service_id', $service_id)->exists()) {
                // Remove o serviço
                $user->services()->detach($service_id);
            } else {
                // Adiciona o serviço
                $user->services()->attach($service_id);
            }

            // Atualiza apenas o funcionário específico no array
            $this->updateEmployeeServices($employee_id);
            
        } catch (\Exception $e) {
            error_log("Erro em changeService: " . $e->getMessage());
        }
    }
    
    private function updateEmployeeServices($employee_id)
    {
        try {
            $user = User::find($employee_id);
            if (!$user) return;
            
            $employeeServices = $user->services()->pluck('service_id')->toArray();
            
            // Atualiza apenas este funcionário no array
            foreach($this->employees as &$employee) {
                if($employee['id'] == $employee_id) {
                    $employee['services'] = $employeeServices;
                    break;
                }
            }
            
        } catch (\Exception $e) {
            error_log("Erro em updateEmployeeServices: " . $e->getMessage());
        }
    }
    public function selectEmployee($employeeId)
    {
        $this->selectedEmployeeId = $employeeId;
    }

    public function updateCustomDuration($serviceId, $employeeId, $duration)
    {
        try {
            $user = User::find($employeeId);
            
            if (!$user || !$user->services()->where('service_id', $serviceId)->exists()) {
                return;
            }
            
            $service = Service::find($serviceId);
            if (!$service) {
                return;
            }
            
            // Se a duração está vazia ou é igual ao tempo padrão, remove a personalização
            if (empty($duration) || $duration == $service->time) {
                $user->services()->updateExistingPivot($serviceId, ['custom_duration_minutes' => null]);
            } else {
                // Salva o tempo personalizado
                $user->services()->updateExistingPivot($serviceId, ['custom_duration_minutes' => intval($duration)]);
            }
            
            // A view se atualiza automaticamente via Livewire reativo
            
        } catch (\Exception $e) {
            error_log("Erro em updateCustomDuration: " . $e->getMessage());
        }
    }



    public function render()
    {
        return view('livewire.proprietario.employee-service', [
            'services' => $this->services, 
            'employees' => $this->employees
        ]);
    }
}
