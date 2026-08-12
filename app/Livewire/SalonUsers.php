<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class SalonUsers extends Component
{
    protected $casts = [
        'editingRoles' => 'array',
    ];
    use WithPagination;

    public $searchTerm = '';
    public $editingUserId = null;
    public $editingRoles = [];
    public $editingStatus = '';
    public $showModal = false;
    public $selectedUser = null;
    public $userDetails = [];
   

    public function showUserDetails($userId)
    {
        $user = User::with('roles')->find($userId);

        // Exemplo de busca dos dados extras:
        $phone = $user->phone ?? '';
        $agendamentos = $user->clientAppointments()->count();
        $ultimoAgendamento = $user->clientAppointments()->latest('appointment_date')->first();

        $temAgendamento = $user->clientAppointments()->where('appointment_date', '>=', now())->exists();
        $currentSubscription = $user->currentSubscription();

        $this->userDetails = [
            'nome' => $user->name,
            'email' => $user->email,
            'phone' => $phone,
            'photo' => $user->photo,
            'funcoes' => $user->roles->pluck('role')->toArray(),
            'agendamentos' => $agendamentos,
            'ultimo_agendamento' => $ultimoAgendamento ? $ultimoAgendamento->appointment_date->format('d/m/Y H:i') : 'Nunca',
            'tem_agendamento' => $temAgendamento ? 'Sim' : 'Não',
            'plano' => $currentSubscription ? $currentSubscription->plan->name : 'Nenhum plano ativo',
            'plano_inicio' => $currentSubscription ? $currentSubscription->start_date->format('d/m/Y') : 'N/A',
            'plano_fim' => $currentSubscription ? $currentSubscription->end_date->format('d/m/Y') : 'N/A',
        ];

        $this->showModal = true;
    }
    public function closeModal()
    {
        $this->showModal = false;
    }

    // Resetar a página ao buscar
    public function updatingSearchTerm()
    {
        $this->resetPage();
    }

    public function editUser($userId)
    {
        $this->editingUserId = $userId;
        $user = User::with('roles')->find($userId);
        $this->editingRoles = $user->roles->pluck('role')->toArray();
        $this->editingStatus = $user->status;
        
    }

    public function saveUser()
    {
        $user = User::find($this->editingUserId);
        if ($user) {
            // Atualiza status
            $user->status = $this->editingStatus;
            $user->save();

            // Atualiza papéis (roles)
            $roleIds = Role::whereIn('role', $this->editingRoles)->pluck('id')->toArray();

            // Limite de funcionários: só bloqueia se está adicionando Funcionário e não tinha antes
            $isAddingFuncionario = in_array('Funcionário', $this->editingRoles) && !$user->roles()->where('role', 'Funcionário')->exists();
            if ($isAddingFuncionario) {
                $currentEmployeeCount = DB::table('branch_user')->count();
                $tenant = tenancy()->tenant;
                $employeeLimit = $tenant->employee_count ?? 1;
                if ($currentEmployeeCount >= $employeeLimit) {
                    session()->flash('error', "Limite de funcionários atingido! Seu plano permite apenas {$employeeLimit} funcionário(s). Para adicionar mais funcionários, atualize seu plano.");
                    $this->editingUserId = null;
                    return;
                }
            }
            $user->roles()->sync($roleIds);

            // Atualiza branch_user conforme papéis
            if (in_array('Funcionário', $this->editingRoles)) {
                DB::table('branch_user')->updateOrInsert(
                    ['user_id' => $user->id],
                    []
                );
            } else {
                DB::table('branch_user')->where('user_id', $user->id)->delete();
            }
        }
        $this->editingUserId = null;
        session()->flash('message', 'Usuário atualizado com sucesso!');
    }

    public function cancelEdit()
    {
        $this->editingUserId = null;
    }

    public function render()
    {
        $salon_users = User::query()
            ->when($this->searchTerm, function($query) {
                $query->where(function($q) {
                    $q->where('name', 'like', '%'.$this->searchTerm.'%')
                      ->orWhere('email', 'like', '%'.$this->searchTerm.'%')
                      ->orWhere('status', 'like', '%'.$this->searchTerm.'%');
                });
            })
            ->orderBy('name', 'asc')
            ->with('roles')
            ->paginate(10);

        

        $roles = Role::pluck('role');
        
        $tenant = tenancy()->tenant;
        $employeeLimit = $tenant->employee_count ?? 1;
        $currentEmployeeCount = DB::table('branch_user')->count();

        return view('livewire.salon-users', [
            'salon_users' => $salon_users,
            'roles' => $roles,
            'employeeLimit' => $employeeLimit,
            'currentEmployeeCount' => $currentEmployeeCount,
        ]);
    }
}